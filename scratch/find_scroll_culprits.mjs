import { spawn } from 'child_process';

const edge = spawn('C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe', [
    '--remote-debugging-port=9520', '--headless=new', '--disable-gpu', '--no-first-run', '--window-size=320,568', 'about:blank'
]);
await new Promise(r => setTimeout(r, 1200));

const v = await fetch('http://127.0.0.1:9520/json/version').then(r => r.json());
const ws = new WebSocket(v.webSocketDebuggerUrl);
await new Promise(r => ws.onopen = r);

let id = 1;
const send = (m, p = {}) => new Promise((resolve) => {
    const reqId = id++;
    const l = (e) => {
        const msg = JSON.parse(e.data);
        if (msg.id === reqId) { ws.removeEventListener('message', l); resolve(msg.result); }
    };
    ws.addEventListener('message', l);
    ws.send(JSON.stringify({ id: reqId, method: m, params: p }));
});

const target = await send('Target.createTarget', { url: 'http://127.0.0.1:8000/posts?q=AI&category=tri-tue-nhan-tao' });
const pws = new WebSocket('ws://127.0.0.1:9520/devtools/page/' + target.targetId);
await new Promise(r => pws.onopen = r);

let pid = 1;
const psend = (m, p = {}) => new Promise((resolve) => {
    const reqId = pid++;
    const l = (e) => {
        const msg = JSON.parse(e.data);
        if (msg.id === reqId) { pws.removeEventListener('message', l); resolve(msg.result); }
    };
    pws.addEventListener('message', l);
    pws.send(JSON.stringify({ id: reqId, method: m, params: p }));
});

await psend('Page.enable');
await psend('DOM.enable');
await psend('Runtime.enable');
await psend('Emulation.setDeviceMetricsOverride', { width: 320, height: 568, deviceScaleFactor: 2, mobile: true });
await new Promise(r => setTimeout(r, 1500));

const res = await psend('Runtime.evaluate', {
    returnByValue: true,
    expression: `(() => {
        const fr = document.getElementById("feed-results");
        const filter = document.querySelector(".feed-filter-indicator");
        const frRect = fr ? fr.getBoundingClientRect() : null;
        const filterRect = filter ? filter.getBoundingClientRect() : null;
        return {
            innerWidth: window.innerWidth,
            feedResultsWidth: frRect ? Math.round(frRect.width) : null,
            feedResultsRight: frRect ? Math.round(frRect.right) : null,
            filterWidth: filterRect ? Math.round(filterRect.width) : null,
            filterRight: filterRect ? Math.round(filterRect.right) : null
        };
    })()`
});

console.log(JSON.stringify(res.result.value, null, 2));
edge.kill();
