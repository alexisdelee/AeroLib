const express = require("express");
const app = express();
const php = require("express-php");

const port = 8080;

function startServer() {
	app.use(php.cgi("public"));
	app.use(express.static("public"));

	server = app.listen(port, "0.0.0.0", () => {
		console.log("port %d in %s mode", port, app.settings.env);
	});
}

if(require.main === module) {
	startServer();
} else {
	module.exports = startServer;
}

app.use((request, response, next) => {
	if(cluster.isWorker) {
		console.log("Worker %d", cluster.worker.id);
	}
	
	return next();
});
