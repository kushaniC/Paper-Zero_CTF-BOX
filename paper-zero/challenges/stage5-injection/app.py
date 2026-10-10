from flask import Flask, request, send_file
import os
app = Flask(__name__)

PAGE = """<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Server Health Dashboard | Paper Zero</title><link rel="stylesheet" href="paper-zero.css"></head><body><main class="pz-shell"><div class="pz-topline"><span>Paper Zero // Operations Console</span><span class="pz-status"><i class="pz-dot"></i> Dashboard online</span></div><section class="pz-panel"><p class="pz-kicker">Stage 05 · Programming / Scripting</p><h1>Server Health Dashboard</h1><p class="pz-copy">Internal diagnostics console. Submit a host address to inspect its network response.</p><hr class="pz-rule"><form class="pz-form"><label class="pz-field">Host to inspect<input name="ip" placeholder="Server IP or hostname" value="{ip}" autocomplete="off"></label><div><button type="submit">Run diagnostics</button></div></form><pre>{out}</pre></section><p class="pz-foot">Operations // Diagnostic output follows</p></main></body></html>"""

@app.route("/paper-zero.css")
def paper_zero_css():
    return send_file("/app/paper-zero.css", mimetype="text/css")

@app.route("/")
def index():
    ip = request.args.get("ip", "")
    out = ""
    if ip:
        # VULNERABLE: user input concatenated straight into a shell command
        out = os.popen("ping -c 1 " + ip).read()
    return PAGE.format(out=out, ip=ip)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
