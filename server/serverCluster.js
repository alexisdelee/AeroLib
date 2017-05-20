const cluster = require("cluster");

function startWorker() {
	let worker = cluster.fork();
	console.log("CLUSTER: Worker %d started", worker.id);
}

if(cluster.isMaster) {
	require("os").cpus().forEach(() => {
		startWorker();
	});
	
	cluster.on("disconnect", (worker) => {
		console.log("CLUSTER: Worker %d disconnected from the cluster", worker.id);
	});

  cluster.on("exit", (worker, code, signal) => {
    console.log("CLUSTER: Worker %d died with exit code %d (%s)", worker.id, code, signal);
    startWorker();
  });
} else {
  require("./server.js")();
}