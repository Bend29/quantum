const http = require("http");
const tail = require('tail-file');

http
.createServer((request, response) => {
    response.writeHead(200, {
        "Connection": "keep-alive",
        "Content-Type": "text/event-stream",
        "Cache-Control": "no-cache",
    });

    const log = new tail('/home/runz.org/quantum/.local/speech.log');

    log.on('line', (line) => {
        response.write('data: ' + line);
        response.write("\n\n");
    }).start();
})
.listen(5000, () => {
    console.log("Server running at http://127.0.0.1:5000/");
});
