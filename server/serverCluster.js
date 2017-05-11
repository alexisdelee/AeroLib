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
		console.log("CLUSTER: Worker %d 
	});
}
