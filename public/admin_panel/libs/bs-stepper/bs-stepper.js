import Stepper from 'bs-stepper/dist/js/bs-stepper';

const bsStepper = document.querySelectorAll('.bs-stepper');

// Adds crossed class
bsStepper.forEach(el => {
  el.addEventListener('show.bs-stepper', function (event) {
    var index = event.detail.indexStep;
    var numberOfSteps = el.querySelectorAll('.line').length;
    var line = el.querySelectorAll('.step');

    // The first for loop is for increasing the steps,
    // the second is for turning them off when going back
    // and the third with the if statement because the last line
    // can't seem to turn off when I press the first item. ¯\_(ツ)_/¯

    for (let i = 0; i < index; i++) {
      line[i].classList.add('crossed');

      for (let j = index; j < numberOfSteps; j++) {
        line[j].classList.remove('crossed');
      }
    }
    if (event.detail.to == 0) {
      for (let k = index; k < numberOfSteps; k++) {
        line[k].classList.remove('crossed');
      }
      line[0].classList.remove('crossed');
    }
  });
});

export { Stepper };
