<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.0/normalize.min.css" />
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
    <style>
      body {
        padding: 20px;
      }
      .vc-container .form-container {
        position: absolute;
        left: 10%;
        top: 10%;
        width: 70%;
        height: 50%;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid white;
        padding: 20px;
        color: black;
        display: none;
      }
      .vc-container .button {
        border: 1px solid blue;
        background: lightblue;
        display: block;
        margin: 5px;
        padding: 0px 5px;
      }
      .vc-container .button:hover {
        color: black;
      }
      .vc-container .clickable {
        cursor: pointer;
      }
      .vc-container a {
        color: blue;
      }
    </style>
  </head>
  <body>
    <h1>Adding forms to the video</h1>
    <h4>interact with your viewers and personlize content</h4>
    <hr>
    <div id="embedBox" style="height: 360px;width:640px;max-width:100%;"></div>
    <script src="https://cdn-gce.vdocipher.com/playerAssets/1.6.4/vdo.js"></script>
    <script>
    let video = new VdoPlayer({
      otp: "20160313versASE313d04b76b86981e66cb6651682f1a2f63a40917ea7e194e9",
      playbackInfo: btoa(JSON.stringify({
        videoId: "3f29b5434a5c615cda18b16a6232fd75"
      })),
      theme: "9ae8bbe8dd964ddc9bdb932cca1cb59a",
      container: document.querySelector( "#embedBox" ),
    });
    let targetQuestion = 3;
    let targetOption = 10;
    let prevTime = 0;
    let formContainer;
    video.addEventListener('mpmlLoad', () => {
      video.injectThemeHtml('<div class="form-container"></div>')
      formContainer = document.querySelector('.form-container');
    });
    video.addEventListener('progress', () => {
      let isCuePointQuestion = prevTime < targetQuestion && video.currentTime > targetQuestion;
      let isCuePointOption = prevTime < targetOption && video.currentTime > targetOption;
      prevTime = video.currentTime;
      if (isCuePointQuestion) {
        video.pause();
        console.log('cue point question');
        createFormQuestion();
      }
      if (isCuePointOption) {
        video.pause();
        console.log('cue point option');
        createOptionOption();
      }
    });

    let createOptionOption = () => {
      formContainer.innerHTML = "";
      let form = document.createDocumentFragment();
      let question = document.createElement('p');
      question.innerHTML = 'Select one of the following option';
      let option1 = document.createElement('button');
      option1.innerHTML = 'Seek to 25th second';
      let option2 = document.createElement('button');
      option2.innerHTML = 'Seek to 33rd second';
      option1.className = 'button clickable';
      option2.className = 'button clickable';
      form.appendChild(question);
      form.appendChild(option1);
      form.appendChild(option2);
      let submitOption = (answer) => {
        console.log('submitted answer to server: ', answer);
        video.play();
        if (answer === 1) video.seek(25);
        if (answer === 2) video.seek(33);
        formContainer.style.display = 'none';
      };
      option1.addEventListener('click', () => submitOption(1));
      option2.addEventListener('click', () => submitOption(2));
      formContainer.appendChild(form);
      formContainer.style.display = 'initial';
    };
    let createFormQuestion = () => {
      formContainer.innerHTML = "";
      let form = document.createDocumentFragment();
      let question = document.createElement('p');
      question.innerHTML = 'What is the answer to life universe and everything?';
      let hint = document.createElement('a');
      hint.innerHTML = 'hint';
      hint.setAttribute('href', 'https://www.google.co.in/search?q=answer+to+life%2C+the+universe+and+everything');
      hint.setAttribute('target', '_blank');
      hint.className = 'clickable';
      let submit = document.createElement('button');
      submit.innerHTML = 'Submit';
      submit.className = 'button clickable';
      let input = document.createElement('input');
      input.setAttribute('style', 'display:block; max-width: 80%;');
      let message = document.createElement('p');
      let continueBtn = document.createElement('button');
      continueBtn.innerHTML = 'Continue';
      continueBtn.className = 'button clickable';
      form.appendChild(question);
      form.appendChild(hint);
      form.appendChild(input);
      form.appendChild(message);
      form.appendChild(submit);
      form.appendChild(continueBtn);
      let submitOption = (answer) => {
        console.log('submitted answer to server: ', answer);
        if (answer.trim() == '42') {
          message.innerHTML = 'Correct answer';
          message.style.color = 'darkgreen';
          message.style.fontWeight = 'bold';
          submit.style.display = 'none';
        } else {
          message.innerHTML = 'Incorrect. Try the hint.';
          message.style.color = 'red';
          message.style.fontWeight = 'bold';
        }

      };
      submit.addEventListener('click', () => submitOption(input.value));
      continueBtn.addEventListener('click', () => {
        video.play();
        formContainer.style.display = 'none';
        formContainer.innerHTML = '';
      });
      formContainer.appendChild(form);
      formContainer.style.display = 'initial';
    }
    </script>
  </body>
</html>