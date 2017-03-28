<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-12">
		<h2>Mailchimp synchronisatie</h2>
		<ol class="breadcrumb">
			<li>
				<a href="index.html">Home</a>
			</li>
			<li>
				<a>Personen</a>
			</li>
			<li class="active">
				<strong>Overzicht</strong>
			</li>
		</ol>
	</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<br />
					<input type="button" class="btn btn-primary" onclick="startTask();"  value="Start synchronisatie" />
					<input type="button" class="btn btn-danger" onclick="stopTask();"  value="Stop synchronisatie" />
					<br />
					<br />

					<p>Resultaten</p>
					<br />
					<div id="results" style="border:1px solid #000; padding:10px; width:100%; height:450px; overflow:auto; background:#eee;"></div>
					<br />

					<progress id='progressor' value="0" max='100' style=""></progress>  <span id="percentage" style="text-align:right; display:block; margin-top:5px;"></span>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
        var es;

        function startTask() {
            es = new EventSource('/mailchimp/do-synchronize');

            //a message is received
            es.addEventListener('message', function (e) {
                var result = JSON.parse(e.data);

                addLog(result.message);

				if(e.lastEventId == 'CLOSE') {
					es.close();
					var pBar = document.getElementById('progressor');
					pBar.value = pBar.max; //max out the progress bar
				}
				else {
					var pBar = document.getElementById('progressor');
					pBar.value = result.progress;
					var perc = document.getElementById('percentage');
					perc.innerHTML   = result.progress  + "%";
					perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
				}
			
			});
		}

		function stopTask() {
			es.close();
			addLog('Onderbroken');
		}

		function addLog(message) {
			var r = document.getElementById('results');
			r.innerHTML += message + '<br>';
			r.scrollTop = r.scrollHeight;
		}
</script>