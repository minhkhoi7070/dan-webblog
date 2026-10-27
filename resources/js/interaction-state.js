/** Serializes explicit state mutations while preserving the latest local intent. */
export class InteractionState {
    constructor({ state, count = null, mutate, reconcile, onChange = () => {}, onError = () => {} }) {
        this.confirmed = Boolean(state);
        this.desired = this.confirmed;
        this.count = Number.isFinite(count) ? Math.max(0, count) : null;
        this.mutate = mutate;
        this.reconcile = reconcile;
        this.onChange = onChange;
        this.onError = onError;
        this.pending = false;
        this.uncertain = false;
        this.revision = 0;
    }

    get snapshot() {
        const visible = this.uncertain ? this.confirmed : this.desired;
        return {
            state: visible,
            confirmed: this.confirmed,
            pending: this.pending,
            uncertain: this.uncertain,
            count: this.count === null ? null : Math.max(0,
                this.count + (visible === this.confirmed ? 0 : visible ? 1 : -1)
            ),
        };
    }

    toggle() {
        const retryUncertain = this.uncertain;
        this.desired = !this.snapshot.state;
        this.uncertain = false;
        this.revision++;
        this.onChange(this.snapshot);
        return this.sync(retryUncertain);
    }

    hydrate(state, count = null, expectedRevision = this.revision) {
        if (!this.pending && expectedRevision === this.revision) {
            this.confirmed = Boolean(state);
            this.desired = this.confirmed;
            this.uncertain = false;
            if (Number.isFinite(count)) this.count = Math.max(0, count);
        }
        this.onChange(this.snapshot);
    }

    applyServer(result) {
        if (typeof result.state !== 'boolean') throw new Error('Invalid interaction response.');
        const previous = this.confirmed;
        this.confirmed = result.state;
        if (Number.isFinite(result.count)) this.count = Math.max(0, result.count);
        else if (this.count !== null && previous !== this.confirmed) {
            this.count = Math.max(0, this.count + (this.confirmed ? 1 : -1));
        }
    }

    async sync(force = false) {
        if (this.pending || (!force && this.desired === this.confirmed)) return;
        this.pending = true;
        this.onChange(this.snapshot);
        try {
            while (force || this.desired !== this.confirmed) {
                force = false;
                const revision = this.revision;
                const target = this.desired;
                try {
                    this.applyServer(await this.mutate(target));
                } catch (error) {
                    let reconciled = false;
                    try {
                        this.applyServer(await this.reconcile());
                        reconciled = true;
                    } catch {
                        this.uncertain = true;
                    }
                    const hasNewerIntent = this.revision !== revision;
                    const desiredBeforeRollback = this.desired;
                    if (!hasNewerIntent) this.desired = this.confirmed;
                    this.onChange(this.snapshot);
                    if (!reconciled || this.confirmed !== desiredBeforeRollback) {
                        this.onError(error, { reconciled });
                    }
                    if (!reconciled || !hasNewerIntent) break;
                }
                this.onChange(this.snapshot);
            }
        } finally {
            this.pending = false;
            this.onChange(this.snapshot);
        }
    }
}
