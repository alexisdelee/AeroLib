const express = require("express");
const app = express();
const php = require("express-php");
const errDomain = require("domain");

const port = 8080;

function startServer() {
	app.use(php.cgi("public"));
	app.use(express.static("public"));

	const server = app.listen(port, "0.0.0.0", () => {
		console.log("port %d in %s mode", port, app.settings.env);
	});
}

if(require.main === module) {
	startServer();
} else {
	module.exports = startServer;
}

app.use((request, response, next) => {
	let domain = errDomain.create();

	domain.on("error", (error) => {
		console.error("DOMAIN ERROR CAUGHT:", error.stack);

		try {
			setTimeout(() => {
				console.error("Failsafe shutdown");

				process.exit(1);
			}, 5000);

			let worker = cluster.worker;

			if(worker) {
				worker.disconnect();
			}

			server.close();

			try {
				next(error);
			} catch(error) {
				console.error("Failed to route Express error");
				response.statusCode = 500;
				response.setHeader("Content-Type", "text/plain");
				response.send("Server error");
			}
		} catch(error) {
			console.error("Unable to send 500 response\n", error.stack);
		}
	});

	domain.add(request);
	domain.add(response);

	domain.run(next);
	
	// return next();
});
