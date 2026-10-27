import test from 'node:test';
import assert from 'node:assert/strict';
import { InteractionState } from '../../resources/js/interaction-state.js';

const deferred = () => {
    let resolve;
    let reject;
    const promise = new Promise((accept, fail) => { resolve = accept; reject = fail; });
    return { promise, resolve, reject };
};
const flush = () => new Promise((resolve) => setImmediate(resolve));

test('ten rapid clicks render immediately and serialize the final target', async () => {
    const requests = [];
    let active = 0;
    let maximumActive = 0;
    const state = new InteractionState({
        state: false, count: 0,
        mutate: (target) => {
            active++;
            maximumActive = Math.max(maximumActive, active);
            const request = deferred();
            requests.push({ target, ...request });
            return request.promise.finally(() => active--);
        },
        reconcile: async () => ({ state: false, count: 0 }),
    });
    const completion = state.toggle();
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
    for (let index = 0; index < 9; index++) state.toggle();
    assert.equal(state.snapshot.state, false);
    assert.equal(state.snapshot.count, 0);
    assert.equal(requests.length, 1);
    requests[0].resolve({ state: true, count: 1 });
    await flush();
    assert.equal(requests.length, 2);
    assert.equal(requests[1].target, false);
    assert.equal(state.snapshot.state, false);
    requests[1].resolve({ state: false, count: 0 });
    await completion;
    assert.equal(state.confirmed, false);
    assert.equal(maximumActive, 1);
});

test('like unlike like coalesces without an unnecessary second mutation', async () => {
    const request = deferred();
    let mutations = 0;
    const state = new InteractionState({
        state: false, count: 4,
        mutate: () => { mutations++; return request.promise; },
        reconcile: async () => ({ state: false, count: 4 }),
    });
    const completion = state.toggle();
    state.toggle();
    state.toggle();
    request.resolve({ state: true, count: 5 });
    await completion;
    assert.equal(mutations, 1);
    assert.equal(state.snapshot.count, 5);
    assert.equal(state.snapshot.state, true);
});

test('a rejected mutation reconciles and rolls back without blindly retrying', async () => {
    let mutations = 0;
    let errors = 0;
    const state = new InteractionState({
        state: false, count: 0,
        mutate: async () => { mutations++; throw new Error('HTTP 500'); },
        reconcile: async () => ({ state: false, count: 0 }),
        onError: () => errors++,
    });
    await state.toggle();
    assert.equal(state.snapshot.state, false);
    assert.equal(state.snapshot.count, 0);
    assert.equal(mutations, 1);
    assert.equal(errors, 1);
});

test('an intent entered during reconciliation is preserved and sent afterward', async () => {
    const first = deferred();
    const recovery = deferred();
    const targets = [];
    const state = new InteractionState({
        state: false, count: 0,
        mutate: (target) => {
            targets.push(target);
            return targets.length === 1 ? first.promise : Promise.resolve({ state: target, count: 1 });
        },
        reconcile: () => recovery.promise,
    });
    const completion = state.toggle();
    state.toggle();
    first.reject(new Error('Network failure'));
    await flush();
    state.toggle();
    recovery.resolve({ state: false, count: 0 });
    await completion;
    assert.deepEqual(targets, [true, true]);
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
});

test('a lost mutation and failed reconciliation expose uncertainty and allow a safe new attempt', async () => {
    let mutations = 0;
    const state = new InteractionState({
        state: false, count: 0,
        mutate: async (target) => {
            mutations++;
            if (mutations === 1) throw new Error('Offline');
            return { state: target, count: 1 };
        },
        reconcile: async () => { throw new Error('Offline'); },
    });
    await state.toggle();
    assert.equal(state.snapshot.state, false);
    assert.equal(state.snapshot.uncertain, true);
    assert.equal(mutations, 1);
    await state.toggle();
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.uncertain, false);
    assert.equal(mutations, 2);
});

test('fragment hydration preserves pending intent and rebases idle state', async () => {
    const request = deferred();
    const state = new InteractionState({
        state: false, count: 0,
        mutate: () => request.promise,
        reconcile: async () => ({ state: false, count: 0 }),
    });
    const completion = state.toggle();
    state.hydrate(false, 0);
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
    request.resolve({ state: true, count: 1 });
    await completion;
    state.hydrate(false, 3);
    assert.equal(state.snapshot.state, false);
    assert.equal(state.snapshot.count, 3);
});

test('optimistic counts never become negative', async () => {
    const state = new InteractionState({
        state: true, count: 0,
        mutate: async () => ({ state: false, count: 0 }),
        reconcile: async () => ({ state: false, count: 0 }),
    });
    const completion = state.toggle();
    assert.equal(state.snapshot.count, 0);
    await completion;
    assert.equal(state.snapshot.count, 0);
});

test('an old feed fragment cannot undo a mutation completed after navigation started', async () => {
    const state = new InteractionState({
        state: false, count: 0,
        mutate: async () => ({ state: true, count: 1 }),
        reconcile: async () => ({ state: true, count: 1 }),
    });
    const revisionAtFeedLoad = state.revision;
    await state.toggle();
    assert.equal(state.pending, false);
    state.hydrate(false, 0, revisionAtFeedLoad);
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
});

test('a fragment requested during a pending mutation preserves its completed result', async () => {
    const request = deferred();
    const state = new InteractionState({
        state: false, count: 0,
        mutate: () => request.promise,
        reconcile: async () => ({ state: true, count: 1 }),
    });
    const completion = state.toggle();
    const pendingAtFeedLoad = state.pending;
    const revisionAtFeedLoad = state.revision;
    request.resolve({ state: true, count: 1 });
    await completion;
    state.hydrate(false, 0, pendingAtFeedLoad ? -1 : revisionAtFeedLoad);
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
});

test('a lost response confirmed by reconciliation does not report a false failure', async () => {
    let errors = 0;
    const state = new InteractionState({
        state: false, count: 0,
        mutate: async () => { throw new Error('Response lost'); },
        reconcile: async () => ({ state: true, count: 1 }),
        onError: () => errors++,
    });
    await state.toggle();
    assert.equal(state.snapshot.state, true);
    assert.equal(state.snapshot.count, 1);
    assert.equal(errors, 0);
});
