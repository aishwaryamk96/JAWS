<?php

/*
           8 8888       .8. `8.`888b                 ,8' d888888o.
           8 8888      .888. `8.`888b               ,8'.`8888:' `88.
           8 8888     :88888. `8.`888b             ,8' 8.`8888.   Y8
           8 8888    . `88888. `8.`888b     .b    ,8'  `8.`8888.
           8 8888   .8. `88888. `8.`888b    88b  ,8'    `8.`8888.
           8 8888  .8`8. `88888. `8.`888b .`888b,8'      `8.`8888.
88.        8 8888 .8' `8. `88888. `8.`888b8.`8888'        `8.`8888.
`88.       8 888'.8'   `8. `88888. `8.`888`8.`88'     8b   `8.`8888.
  `88o.    8 88'.888888888. `88888. `8.`8' `8,`'      `8b.  ;8.`8888
    `Y888888 ' .8'       `8. `88888. `8.`   `8'        `Y8888P ,88P'

    JIGSAW ACADEMY WORKFLOW SYSTEM v2
    ---------------------------------
*/

  	// Prevent exclusive access
	if (!defined("JAWS")) {
		header('Location: https://www.jigsawacademy.com');
		die();
	}

    	// Load stuff
    	load_module("iot");

    	// Init View
    	iot_view_init();

?>

  <!-- HTML HEAD -->
  <?php
  $GLOBALS["content"]["title"] = "FAQS about IoT";
  $GLOBALS["content"]["meta_description"] = "Learn about Internet of Things, IoT Courses and Specializations, IoT Training, Certification, Kit and others at";
	load_template("iot", "v2/head");
  ?>
  <!-- HTML HEAD ENDS -->

  <!-- HEADER MENU -->
  <?php load_template("iot", "v2/header"); ?>
  <!-- HEADER MENU ENDS -->

<!-- Banner Start -->
<div class="gray-bg" id="side_nav_item_id_0">
	<section class="wrapper breadcrumb-start">
		<ol class="bread-crumbs">
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="https://www.jigsawacademy.com/iot/" itemprop="url" class="blue-text">
						<span itemprop="title">IoT Home</span>
					</a>
				</span>
			</li>
			<li>
				<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
					<a href="https://www.jigsawacademy.com/iot/faqs" itemprop="url" class="blue-text last">
						<span itemprop="title">IoT FAQS</span>
					</a>
				</span>
			</li>
		</ol>
	</section>
  <div class="wrapper header-padding-top">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <h1 class="innerpage-banner-title f-color-blue">Frequently Asked Questions</h1>
        <!--<p class="banner-contain">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>-->
        <div class="margin-30 padding-bottom-10"></div>

      </div>
    </div>
  </div>
</div>
<!-- Banner End -->

<!-- curriculum section start -->
<div class="border-bottom" id="curriculum-section">
  <div class="wrapper section-padding-top section-padding-bottom relative">


    <div class="row">
     <div class="col-lg-2 col-md-2 col-sm-2"></div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
              <h2 class="innerpage-title">About IoT</h2>
                <div class="gary-strip"></div>
                <div class="margin-30"></div>
        <div class="demo">
          <div class="panel-group panel-color" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default custom-panel">
              <div class="panel-heading custom-heding-panel" role="tab" id="heading1">
                <h4 class="panel-title">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapse1" href="javascript:;" aria-expanded="true" aria-controls="collapse1">What will be the impact of IoT on our careers?</a>
                </h4>
              </div>
              <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
                <div class="panel-body">
          <p>IoT is the next big revolution in tech space and it has already started having a huge impact on both businesses and consumers. Every fifth developer today is working on IoT related projects. New job profiles like IoT Managers and IoT Architects have been created, which never existed earlier.</p>
          <p>Visit our Careers and Explore section on the website to learn more about IoT.</p>
        </div>
              </div>
            </div>
            <div class="panel panel-default custom-panel">
              <div class="panel-heading custom-heding-panel" role="tab" id="heading2">
                <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">How are IoT and Analytics connected?</a> </h4>
              </div>
              <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
                <div class="panel-body">
          <p>EMC estimates that IoT will account for 4.4 trillion GB of the data in the digital universe by 2020. However, the real value lies in the intersection of gathering data and leveraging it. This will mean the need to have new data management and integration approaches, and new ways to analyze streaming data continuously. GE estimates that convergence of machines, data and analytics will become a $200 billion global industry over the next three years!</p>
                </div>
              </div>
            </div>

     <div class="margin-30"></div>
         <h2 class="innerpage-title">Courses and Specializations</h2>
     <div class="gary-strip"></div>
     <div class="margin-30"></div>

          <div class="panel panel-default custom-panel">
              <div class="panel-heading custom-heding-panel" role="tab" id="heading5">
                <h4 class="panel-title">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">Do I need to take the courses in a specific order?</a>
                </h4>
              </div>
              <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
                <div class="panel-body">
          <p>We recommend taking the courses in the order presented, as each course will build on material from previous courses.</p>
                </div>
              </div>
            </div>


          <div class="panel panel-default custom-panel">
              <div class="panel-heading custom-heding-panel" role="tab" id="heading6">
                <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse6" aria-expanded="true" aria-controls="collapse6">What are Specializations?</a> </h4>
              </div>
              <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
                <div class="panel-body">
          <p>Specializations help you know which courses to take in what specific order, so that you can work toward a specific job role.</p>
          <p>Jigsaw Academy has crafted certain Specializations based on the skills that are in demand in the industry. You can maximize your chances of entering the field of core IoT and IoT Analytics with these specializations.</p>
                </div>
              </div>
            </div>

             <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading7">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse7" aria-expanded="true" aria-controls="collapse7">How do I choose between a Specialization and a course? </a> </h4>
                  </div>
                  <div id="collapse7" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading7">
                      <div class="panel-body">
                          <p>Whether you choose a Specialization or a course will depend on your learning goal.  You can speak to a Jigsaw Academy counselor at +91 90193 17000 if you still have questions.</p>
                      </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading10">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">Why should I go for a Specialization instead of one single course?</a> </h4>
                  </div>
                  <div id="collapse10" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading10">
                    <div class="panel-body">
                          <p>Specializations are designed to give you the combination of skills that’s most in-demand in the industry. For example, the IoT Professional Specialization will help you gain a solid understanding of how to develop and implement your own IoT solutions and applications using Arduino and Raspberry Pi. This course will prepare you for the role of an IoT Architect.
                             You can speak to a Jigsaw Academy counselor if you still have questions.</p>
                    </div>
                  </div>
          </div>

            <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading11">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse11" aria-expanded="true" aria-controls="collapse11">I want a combination of courses that’s not in any of the Specializations. What should I do? </a> </h4>
                  </div>
                  <div id="collapse11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading11">
                    <div class="panel-body">
                          <p>You can enroll for multiple courses on our website or you can speak to a Jigsaw counselor who can help you create your own personal learning path. However, you need to complete a few courses in an order to take up the advanced level.</p>
                    </div>
                  </div>
              </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading15">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse15" aria-expanded="true" aria-controls="collapse15">Can I just enroll in a single course? I'm not interested in the entire Specialization.</a> </h4>
                  </div>
                  <div id="collapse15" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading15">
                      <div class="panel-body">
                          <p>Yes—to enroll in an individual course, search for the course title in the catalog. Scroll down to the "Courses" section, below any related Specializations, to select it.</p>
                      </div>
                  </div>
          </div>

          <!-- faq added by  Nikita as per the requirement - 09-02-2017 -->
           <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading27">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse27" aria-expanded="true" aria-controls="collapse27">How long will I have access to my course?</a> </h4>
                  </div>
                  <div id="collapse27" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading27">
                      <div class="panel-body">
                          <p>For each individual course, you will have access for upto 4 months. If you have signed up for a specialization, which is a combination of courses, you will have access for upto 9 months. If you have signed up for more than a single specialization, or the Full Stack IoT Expert specialization, your access will be for 15 months.</p>
                      </div>
                  </div>
          </div>

     <div class="margin-30"></div>
         <h2 class="innerpage-title">IoT Training at Jigsaw</h2>
     <div class="gary-strip"></div>
     <div class="margin-30"></div>

             <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading8">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse8" aria-expanded="true" aria-controls="collapse8">How to solve my doubts during the course?</a> </h4>
                  </div>
                  <div id="collapse8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading8">
                    <div class="panel-body">
                        <p>Doubts can be asked via forum, email or chat sessions (Google hangouts). Once the course is over, you can still get in touch with us via email.</p>
                    </div>
                  </div>
              </div>

                <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading9">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">Is this an online course? How will you give hardware training online? </a> </h4>
                  </div>
                  <div id="collapse9" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading9">
                    <div class="panel-body">
                          <p>You will be part of the revolutionary <a href="https://www.jigsawacademy.com/how-online-training-works/"><b>Catalyst</b></a> approach to learning. We will be providing several ‘how-to’ videos and instructables as part of the learning material so that you can practice on the exercises shown in the videos during the course. We advise students to work on the hardware alongside the instructor for maximum learning effectiveness. There will also be a capstone project which you will have to work on that will aggregate all the learnings till then. </p>
                    </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading16">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse16" aria-expanded="true" aria-controls="collapse16">What is a Capstone project?</a> </h4>
                  </div>
                  <div id="collapse16" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading16">
                    <div class="panel-body">
                          <p>A capstone project is a multifaceted assignment that serves as a culminating academic and intellectual experience for students, typically at the end of an academic program or learning-pathway experience.  It helps you practice, apply, and showcase the skills you’ve learned.</p>
                    </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading17">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse17" aria-expanded="true" aria-controls="collapse17">Who can take up this course? </a> </h4>
                  </div>
                  <div id="collapse17" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading17">
                      <div class="panel-body">
                          <p>The Internet of Things is applicable to all verticals and specializations. That's the beauty and attraction of IoT. Anyone with basic programming skills can take up Jigsaw’s IoT courses.</p>
                      </div>
                  </div>
          </div>

          <!-- Faq added by Nikita as per the requirement - 09-02-2017 -->
           <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading28">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse28" aria-expanded="true" aria-controls="collapse28">Do you help with placements?</a> </h4>
                  </div>
                  <div id="collapse28" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading28">
                      <div class="panel-body">
                          <p>Our courses have been custom-created keeping in mind industry requirements. We will assist you in identifying the right opportunities based on the skill sets you will gain through our courses.</p>
                      </div>
                  </div>
          </div>

                 <div class="margin-30"></div>
                     <h2 class="innerpage-title">Certification</h2>
                 <div class="gary-strip"></div>
                 <div class="margin-30"></div>


             <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading12">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse12" aria-expanded="true" aria-controls="collapse12">Do I get a certificate at the end of my course?</a> </h4>
                  </div>
                  <div id="collapse12" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading12">
                    <div class="panel-body">
            <p>Yes, you will get a certificate at the end of every Jigsaw Specialisation(except for IoT beginners). For more details, you can check the Certification section of each of the Specialisation you’re enrolling for.</p>
                    </div>
                  </div>
                </div>

                 <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading13">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse13" aria-expanded="true" aria-controls="collapse13">By when will I get my certificate after course completion?</a> </h4>
                  </div>
                  <div id="collapse13" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading13">
                    <div class="panel-body">
            <p>Upon completion of your specialization, you need to apply for a certification test. Once you have successfully cleared the certification test, you will get your certificate within 1 month.</p>
                    </div>
                  </div>
                </div>

                 <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading14">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse14" aria-expanded="true" aria-controls="collapse14">How helpful will these courses be in the real job scenario?</a> </h4>
                  </div>
                  <div id="collapse14" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading14">
                    <div class="panel-body">
            <p>The IoT courses by Jigsaw have been designed by industry veterans who have a good understanding of IoT and have rich work experience in IoT related domains. We have taken extensive industry feedback on the course content to cover concepts which will be relevant and much needed in your actual job roles.  Each of our specializations is mapped to specific job roles and prepares you accordingly.</p>
            <p>You can speak to a Jigsaw Academy counselor at +91 90193 17000 if you still have any queries.</p>
          </div>
                  </div>
                </div>

        <div class="margin-30"></div>
                     <h2 class="innerpage-title">Kit Related</h2>
                 <div class="gary-strip"></div>
                 <div class="margin-30"></div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading18">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse18" aria-expanded="true" aria-controls="collapse18">What kind of hardware will be used for this training?</a> </h4>
                  </div>
                  <div id="collapse18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading18">
                    <div class="panel-body">
            <p>The kit will contain an Arduino and a Raspberry Pi along with sensors and other basic electronic circuit components such as the breadboard, resistors etc. </p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading19">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse19" aria-expanded="true" aria-controls="collapse19">How I will get my hardware kit?</a> </h4>
                  </div>
                  <div id="collapse19" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading19">
	                    <div class="panel-body">
		            	<p>The IoT hardware kit is included with your enrollment and will be shipped by Jigsaw Academy (only within India). There are separate kits available depending on your choice of specialization. Please visit our courses section on the IoT website (https://www.jigsawacademy.com/IoT/courses) for more information.</p>
		          </div>
                  </div>
         </div>

          <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading30">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse30" aria-expanded="true" aria-controls="collapse30">How long will it take to receive my IOT hardware kit?</a> </h4>
                  </div>
                  <div id="collapse30" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading30">
	                    <div class="panel-body">
		            	<p>Please note that it will take at least 10 days from the date of enrollment for delivery of the kit.</p>
		          </div>
                  </div>
         </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading20">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse20" aria-expanded="true" aria-controls="collapse20">I would like to purchase components on my own? How can I do that?</a> </h4>
                  </div>
                  <div id="collapse20" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading20">
                    <div class="panel-body">
            <p>The IoT kit provided with the course contains all the necessary equipment that you will need to learn. We advise using our kit for best learning results.</p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading21">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse21" aria-expanded="true" aria-controls="collapse21">Will there be any replacement if any of the components don’t work?</a> </h4>
                  </div>
                  <div id="collapse21" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading21">
                    <div class="panel-body">
            <p>In case you receive damaged or defective products, please report the same to our support team. This should be reported within 7 days of receiving the damaged / malfunctioning products. Please note that this policy only covers replacements for eligible products that are defective, or that were not shipped as ordered. It will not cover routine product wear and tear, damage incurred during use or any other forms of damage and will not, in any event, entitle you to a refund, whether partial or otherwise.</p>
          </div>
                  </div>
                </div>

        <div class="margin-30"></div>
                     <h2 class="innerpage-title">Enrollment and Payment</h2>
                 <div class="gary-strip"></div>
                 <div class="margin-30"></div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading22">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse22" aria-expanded="true" aria-controls="collapse22">What are the different modes of payment available for enrollment?</a> </h4>
                  </div>
                  <div id="collapse22" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading22">
                    <div class="panel-body">
            <p>Participants can pay through an online transfer, debit card or credit card (Visa, Master Card and Amex). Foreign students can pay via credit card or PayPal.</p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading23">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse23" aria-expanded="true" aria-controls="collapse23">Can the fee be paid in instalments?</a> </h4>
                  </div>
                  <div id="collapse23" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading23">
                    <div class="panel-body">
            <p>EMI options are available on select credit cards. For more options, please contact Jigsaw Academy counselors.</p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading24">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse24" aria-expanded="true" aria-controls="collapse24">After payment, when will I get access to my course?</a> </h4>
                  </div>
                  <div id="collapse24" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading24">
                    <div class="panel-body">
            <p>After payment, students need to setup their access. Once we have your access details, we will enable it for you within 2 days.</p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading25">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse25" aria-expanded="true" aria-controls="collapse25">What are the payment options?</a> </h4>
                  </div>
                  <div id="collapse25" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading25">
                    <div class="panel-body">
            <p>You can pay for the entire Specialization upfront; or pay individually for each course as you progress. We advise enrolling for a specialization since they are matched to specific job roles and are also cost-effective.</p>
          </div>
                  </div>
                </div>

        <div class="panel panel-default custom-panel">
                  <div class="panel-heading custom-heding-panel" role="tab" id="heading26">
                    <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:;" data-target="#collapse26" aria-expanded="true" aria-controls="collapse26">What is the refund policy?</a> </h4>
                  </div>
                  <div id="collapse26" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading26">
                    <div class="panel-body">
            <p>Individual courses that are less than 10 hours in duration are not eligible for refund. If you enroll for a specialization, you can receive a full refund amount excluding the price of the kit, if a refund request is placed, up to 7 days (one week) after payment. For any other queries, please reach out to our support team.</p>
          </div>
                  </div>
                </div>
          </div>
        </div>
    <div class="margin-30"></div>
      <p><b>In case you still have any unanswered questions, you can always write an email to us at info@jigsawacademy.com or call us at +91 90193 17000</b></p>


      </div>

      <div class="col-lg-2 col-md-2 col-sm-2"></div>

    </div>

        </div>
      </div>

    </div>


  </div>
</div>
<!-- courses section end -->

<!-- gray-band start -->
<div class="wrapper section-padding-top section-padding-bottom">
  <div class="row spe-gray-band">
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
      <h3 class="spe-gray-band-text f-color-blue font-19">4 courses. 1 certification. Rs 14,000 in savings. Become a certified IoT professional</h3>
      </span> </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
      <div class="text-center-xs actionbtn skew-btn spe-btn-padding uppercase"><a target="_blank" <?php iot_a_part('https://www.jigsawacademy.com/checkout-iot/?c='.$GLOBALS['iot_courses']['bundles'][0]["wp_id"].'&p=path','#loginmodal','#profile-popup1'); ?> ><span>Enroll Now</span>
        </a></div>
    </div>
  </div>
</div>
<!-- gray-band end -->


  <!-- FOOTER -->
  <?php load_template("iot", "v2/footer"); ?>
  <!-- FOOTER ENDS -->

  <!-- LOGIN MODAL -->
  <?php load_template("iot", "v2/login"); ?>
  <!-- LOGIN MODAL ENDS -->

  <script>
var menu = document.querySelector('.menu');
function toggleMenu () {
  menu.classList.toggle('open');
}
menu.addEventListener('click', toggleMenu);
</script>

  <script>
$(document).ready(function() {

  $("#owl-demo").owlCarousel({

      autoPlay: 3000, //Set AutoPlay to 3 seconds

      items : 3,
      itemsDesktop : [1199,3],
      itemsDesktopSmall : [979,2]

  });

});
</script>
<!-- FOOTER -->
	<?php load_template("iot", "v2/foot"); ?>
<!-- FOOTER ENDS -->