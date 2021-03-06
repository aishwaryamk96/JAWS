<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
    crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha256-3edrmyuQ0w65f8gfBsqowzjJe2iM6n0nKciPUp8y+7E="
    crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    <?php
        require('styles.css');
    ?>
</style>
<link rel="stylesheet" href="http://cloudlab.jigsawacademy.in/rlab/templates/template.css">
<?php
    if($isDyanmicIp){
?>
<script>
    function generateLabInstance(){
        // console.log('<?php echo $_POST["course_id"] ?>');
        fetch('https://www.jigsawacademy.com/jaws/labapi/blog').then(r=>r.text())
        .then(r=>{
            // console.log(r)
            // Set the date we're counting down to
            var countDownTime = Date.now()+(4*60*1000)

            // Update the count down every 1 second
            var x = setInterval(function() {

                // Get todays date and time
                var now = new Date().getTime();

                // Find the distance between now an the count down date
                var distance = countDownTime - now;
                
                // Time calculations for days, hours, minutes and seconds
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Output the result in an element with id="demo"
                document.getElementById("clock").innerHTML =  minutes + "m " + seconds + "s ";
                
                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("clock").innerHTML = "YOUR INSTANCE IS ACTIVE";
                }
            }, 1000);
            $('#wait-box').addClass('d-flex')
            $('#blogs').html(r)
            $('.blog-article').click(function(e){
                var target=e.target
                if(!($(target).hasClass('fa-times'))){
                    var id = $(this).attr('id');
                    $('#'+id).addClass('max')
                }
            })
            $('.article-close').click(function (e) {
                var blogArticle =e.currentTarget
                let parentId =$(this).parent().attr('id')
                let max = 'max'
                $('#'+parentId).removeClass(max)
            })
            for(let j=0;j<=6;j++){
                $(`#blogs .blog-article:nth-child(${j})`).attr('id',`${j}-blog-article`)
            }
            for(let i=7;i<=$('#blogs .blog-article').length;i++){
                $(`#blogs .blog-article:nth-child(${i})`).addClass('hidden')
            }
            window.location.href='#timer'
        })
        let headers ='Bearer expected-really-long-token'
        let payload =new FormData()
        payload.append('user',"<?php echo $a[0]["lab_user"]?>")
        payload.append('course_id',"<?php echo $_POST["course_id"]?>")
        fetch('https://www.jigsawacademy.com/jaws/labapi/validate-lab',{
            method: 'POST',
            body: payload,
            headers: {
                // 'Authorization': payload
            }
        }).then(r=>r.text())
        .then(response=>{
            console.log('response')
        })
        .catch(e=>{
            console.log('error',e)
        })
    }
</script>
<?php }?>
<div class='container'>
    <p style="text-align: center;">&nbsp;</p>
    <p style="text-align: center;">&nbsp;</p>
    <div class="container" style='margin-bottom: 1%;'>
        <img src="http://via.placeholder.com/200x150" alt="" style="max-width:100%;height:150px;" class='col-xs-12 col-sm-4 col-md-2 hidden-xs ' style='margin-left:-24px;'>
        <div class="col-xs-12 col-sm-8 col-md-10 title-text">
            <p style="text-align: left;">
                <span style="color: #000000; font-size: large;">
                     Instructions on Jigsaw Lab
                </span>
            </p>
            <p>
                Jigsaw Lab is a Virtual Lab that allows you to access the software used in the course through your Web browser. With the Virtual Lab, you don't need the required software on your local machine to complete the course exercises.
            </p>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempora aliquid praesentium dolorem. Eveniet ab a voluptatibus obcaecati adipisci ullam, provident tenetur numquam molestias, dolorum, repellat quaerat quo odit corrupti quod?
            </p>
            <?php
                $attr=$isDyanmicIp?'onclick="generateLabInstance()"':'href="lab-url"';
            ?>
            <a class="btn btn-warning col-xs-10 col-sm-5 col-md-4" <?php echo $attr; ?>>
               Connect to lab
            </a>
        </div>
    </div>
    <div id="blogs">
    </div>
    <div id="timer" style='height:10vh;width:100%;'></div>
    <div id='wait-box' >
        <p>It takes approx. 5 mins for the lab to be configured.</p>
        <p id="clock"></p>
    </div>
    <!-- <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style='width:100%' id='1'>
                    <div class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" >
                        <div class="arrow-cont">
                            <img class="arrow" id="arrow1" src='https://www.techwalla.com/ui/images/icons/down4.svg' style='height:18px;width:15px;transform:rotate(270deg)'
                            />
                        </div>
                        <div>&nbsp;</div>
                        <div>
                            Full Stack Data Science Program Lab
                        </div>
                    </div>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">
					<div class='table col-xs-12 col-sm-10 col-md-8'>
						<ul>
							<li class='row'>
								<div class='col-xs-12 col-sm-10 col-md-8 table-text'>
									 Modules
								</div>
							</li>
						<li class='row'>
								<div class='col-xs-6 col-sm-5 col-md-4 table-text'>Overview of analytics</div>
								<div class='col-xs-6 col-sm-5 col-md-4 gray'>
									<span>
										<i class='fa fa-desktop fa-lg' title='no lab'></i>
									</span>
								</div>
						</li>
						<li class='row'>
								<div class='col-xs-6 col-sm-5 col-md-4 table-text'>Descriptive analytics with statistics</div>
								<div class='col-xs-6 col-sm-5 col-md-4 green'>
									<span>
										<i class='fa fa-desktop fa-lg' title='lab available'></i>
									</span>
									<span  class='vm-span' title="download vm">
                                         VM
									</span>
									<span>
										<i class='fa fa-question-circle fa-lg' title="help"></i>
									</span>
								</div>
						</li>
						<li class='row'>
								<div class='col-xs-6 col-sm-5 col-md-4 table-text'>R for Data Science</div>
								<div class='col-xs-6 col-sm-5 col-md-4 green'>
									<span>
										<i class='fa fa-desktop fa-lg' title='lab available'></i>
									</span>
									<span  class='vm-span' title="download vm">
										 VM
									</span>
									<span>
										<i class='fa fa-question-circle  fa-lg' title="help"></i>
									</span>
								</div>
						</li>
						<li class='row'>
								<div class='col-xs-6 col-sm-5 col-md-4 table-text'>Data Wrangling and EDA with R</div>
								<div class='col-xs-6 col-sm-5 col-md-4 gray'>
								  <span>
									<i class='fa fa-desktop fa-lg' title='no lab'></i>
								  </span>
								</div>
						</li>
						<li class='row'>
								<div class='col-xs-6 col-sm-5 col-md-4 table-text'>Testing Hypothesis with Data</div>
								<div class='col-xs-6 col-sm-5 col-md-4 green'>
								  <span>
									<i class='fa fa-desktop fa-lg' title='lab available'></i>
								  </span>
								  <span  class='vm-span' title="download vm">
                                     VM
								  </span>
								  <span>
									<i class='fa fa-question-circle fa-lg' title="help"></i>
								  </span>
								</div>
						</li>
						<li class='row'>
							<div class='col-xs-6 col-sm-5 col-md-4 table-text'>Predictive analytics with R</div>
							<div class='col-xs-6 col-sm-5 col-md-4 green'>
                                <span>
                                    <i class='fa fa-desktop fa-lg'title='lab available'></i>
                                </span>
                                <span  class='vm-span' title="download vm">
                                     VM
                                </span>
                                <span>
                                    <i class='fa fa-question-circle fa-lg' title="help"></i>
                                </span>
                            </div>
                        </li>
                        <li class='row'>
                            <div class='col-xs-12 col-sm-10 col-md-8'>

                            </div></li>
					</ul>
				</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" style='width:100%' id='2'>
                    <div class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" style='width:100%;cursor:pointer;display: flex;align-items: flex-start;'>
                        <div class="arrow-cont">
                            <img class="arrow" id="arrow2" src='https://www.techwalla.com/ui/images/icons/down4.svg' style='height:18px;width:15px;transform:rotate(270deg)'
                            />
                        </div>
                        <div>&nbsp;</div>
                        <div>
                          Troubleshooting
                        </div>
                    </div>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    <h4>Top Troubleshooting points</h4>
                    <ul>
                        <li>Quick steps to access lab</li>
                        <ol>
                            <li>sdfn,</li>
                            <li>sdfnsdfs</li>
                            <li>asdnkla ds</li>
                        </ol>
                        <li>Can't access lab</li>
                        <ol>
                            <li>sdfn,</li>
                            <li>sdfnsdfs</li>
                            <li>asdnkla ds</li>
                        </ol>
                    </ul>
                    <div class="btn btn-warning col-xs-10 col-sm-5 col-md-4">
                        View Complete Troubleshooting document
                    </div>
                    <br/>
                    <br/>
                    <p>
                        If your query is still not answered raise a support ticket
                    </p>
                </div>
            </div>
    </div> -->
    </div>
    <!-- end container -->
    <script>
        $('.panel-title').click(function (e) {
            let tar = $(e.currentTarget).attr('id')
            console.log(`arrow${tar}`, $(`.arrow${tar}`))
            $(`#arrow${tar}`).toggleClass("rotated")
        })
    </script>
</div>