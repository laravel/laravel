/**
 * Sweet Alerts
 */

'use strict';

(function () {
  const basicAlert = document.querySelector('#basic-alert'),
    withTitle = document.querySelector('#with-title'),
    footerAlert = document.querySelector('#footer-alert'),
    htmlAlert = document.querySelector('#html-alert'),
    positionTopStart = document.querySelector('#position-top-start'),
    positionTopEnd = document.querySelector('#position-top-end'),
    positionBottomStart = document.querySelector('#position-bottom-start'),
    positionBottomEnd = document.querySelector('#position-bottom-end'),
    bounceInAnimation = document.querySelector('#bounce-in-animation'),
    fadeInAnimation = document.querySelector('#fade-in-animation'),
    flipXAnimation = document.querySelector('#flip-x-animation'),
    tadaAnimation = document.querySelector('#tada-animation'),
    shakeAnimation = document.querySelector('#shake-animation'),
    iconSuccess = document.querySelector('#type-success'),
    iconInfo = document.querySelector('#type-info'),
    iconWarning = document.querySelector('#type-warning'),
    iconError = document.querySelector('#type-error'),
    iconQuestion = document.querySelector('#type-question'),
    customImage = document.querySelector('#custom-image'),
    autoClose = document.querySelector('#auto-close'),
    outsideClick = document.querySelector('#outside-click'),
    progressSteps = document.querySelector('#progress-steps'),
    ajaxRequest = document.querySelector('#ajax-request'),
    confirmText = document.querySelector('#confirm-text'),
    confirmColor = document.querySelector('#confirm-color');

  // Basic Alerts
  // --------------------------------------------------------------------

  // Default Alert
  if (basicAlert) {
    basicAlert.onclick = function () {
      Swal.fire({
        title: 'Any fool can use a computer',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alert With Title
  if (withTitle) {
    withTitle.onclick = function () {
      Swal.fire({
        title: 'The Internet?,',
        text: 'That thing is still around?',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alert With Footer
  if (footerAlert) {
    footerAlert.onclick = function () {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Something went wrong!',
        footer: '<a href>Why do I have this issue?</a>',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Html Alert
  if (htmlAlert) {
    htmlAlert.onclick = function () {
      Swal.fire({
        title: '<strong>HTML <u>example</u></strong>',
        icon: 'info',
        html:
          'You can use <b>bold text</b>, ' +
          '<a href="https://pixinvent.com/" target="_blank">links</a> ' +
          'and other HTML tags',
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
        confirmButtonAriaLabel: 'Thumbs up, great!',
        cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
        cancelButtonAriaLabel: 'Thumbs down',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alerts Positions
  // --------------------------------------------------------------------

  // Top Start Alert
  if (positionTopStart) {
    positionTopStart.onclick = function () {
      Swal.fire({
        position: 'top-start',
        icon: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Top End Alert
  if (positionTopEnd) {
    positionTopEnd.onclick = function () {
      Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Bottom Start Alert
  if (positionBottomStart) {
    positionBottomStart.onclick = function () {
      Swal.fire({
        position: 'bottom-start',
        icon: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Bottom End Alert
  if (positionBottomEnd) {
    positionBottomEnd.onclick = function () {
      Swal.fire({
        position: 'bottom-end',
        icon: 'success',
        title: 'Your work has been saved',
        showConfirmButton: false,
        timer: 1500,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alerts With Animations
  // --------------------------------------------------------------------

  // Bounce In Animation
  if (bounceInAnimation) {
    bounceInAnimation.onclick = function () {
      Swal.fire({
        title: 'Bounce In Animation',
        showClass: {
          popup: 'animate__animated animate__bounceIn'
        },
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Fade In Animation
  if (fadeInAnimation) {
    fadeInAnimation.onclick = function () {
      Swal.fire({
        title: 'Fade In Animation',
        showClass: {
          popup: 'animate__animated animate__fadeIn'
        },
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Flip X Animation
  if (flipXAnimation) {
    flipXAnimation.onclick = function () {
      Swal.fire({
        title: 'Flip In Animation',
        showClass: {
          popup: 'animate__animated animate__flipInX'
        },
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Tada Animation
  if (tadaAnimation) {
    tadaAnimation.onclick = function () {
      Swal.fire({
        title: 'Tada Animation',
        showClass: {
          popup: 'animate__animated animate__tada'
        },
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Shake Animation
  if (shakeAnimation) {
    shakeAnimation.onclick = function () {
      Swal.fire({
        title: 'Shake Animation',
        showClass: {
          popup: 'animate__animated animate__shakeX'
        },
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alert Types
  // --------------------------------------------------------------------

  // Success Alert
  if (iconSuccess) {
    iconSuccess.onclick = function () {
      Swal.fire({
        title: 'Good job!',
        text: 'You clicked the button!',
        icon: 'success',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Info Alert
  if (iconInfo) {
    iconInfo.onclick = function () {
      Swal.fire({
        title: 'Info!',
        text: 'You clicked the button!',
        icon: 'info',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Warning Alert
  if (iconWarning) {
    iconWarning.onclick = function () {
      Swal.fire({
        title: 'Warning!',
        text: ' You clicked the button!',
        icon: 'warning',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Error Alert
  if (iconError) {
    iconError.onclick = function () {
      Swal.fire({
        title: 'Error!',
        text: ' You clicked the button!',
        icon: 'error',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Question Alert
  if (iconQuestion) {
    iconQuestion.onclick = function () {
      Swal.fire({
        title: 'Question!',
        text: ' You clicked the button!',
        icon: 'question',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Advanced Options
  // --------------------------------------------------------------------

  //Alert With Custom Icon
  if (customImage) {
    customImage.onclick = function () {
      Swal.fire({
        title: 'Sweet!',
        text: 'Modal with a custom image.',
        imageUrl: assetsPath + 'img/backgrounds/15.jpg',
        imageWidth: 400,
        imageAlt: 'Custom image',
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Auto Closing Alert
  if (autoClose) {
    autoClose.onclick = function () {
      var timerInterval;
      Swal.fire({
        title: 'Auto close alert!',
        html: 'I will close in <strong></strong> seconds.',
        timer: 2000,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false,
        willOpen: function () {
          Swal.showLoading();
          timerInterval = setInterval(function () {
            Swal.getHtmlContainer().querySelector('strong').textContent = Swal.getTimerLeft();
          }, 100);
        },
        willClose: function () {
          clearInterval(timerInterval);
        }
      }).then(function (result) {
        if (
          // Read more about handling dismissals
          result.dismiss === Swal.DismissReason.timer
        ) {
          console.log('I was closed by the timer');
        }
      });
    };
  }

  // Close Alert On Backdrop Click
  if (outsideClick) {
    outsideClick.onclick = function () {
      Swal.fire({
        title: 'Click outside to close!',
        text: 'This is a cool message!',
        backdrop: true,
        allowOutsideClick: true,
        customClass: {
          confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
      });
    };
  }

  // Alert With Steps
  if (progressSteps) {
    progressSteps.onclick = function () {
      const steps = ['1', '2', '3'];
      const swalQueueStep = Swal.mixin({
        confirmButtonText: 'Forward',
        cancelButtonText: 'Back',
        progressSteps: steps,
        input: 'text',
        inputAttributes: {
          required: true
        },
        validationMessage: 'This field is required'
      });

      async function backAndForward() {
        const values = [];
        let currentStep;

        for (currentStep = 0; currentStep < steps.length; ) {
          const result = await new swalQueueStep({
            title: 'Question ' + steps[currentStep],
            showCancelButton: currentStep > 0,
            currentProgressStep: currentStep
          });

          if (result.value) {
            values[currentStep] = result.value;
            currentStep++;
          } else if (result.dismiss === 'cancel') {
            currentStep--;
          }
        }

        Swal.fire(JSON.stringify(values));
      }

      backAndForward();
    };
  }

  // Alert With Ajax Request
  if (ajaxRequest) {
    ajaxRequest.onclick = function () {
      Swal.fire({
        title: 'Submit your Github username',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Look up',
        showLoaderOnConfirm: true,
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-danger'
        },
        preConfirm: login => {
          return fetch('//api.github.com/users/' + login)
            .then(response => {
              if (!response.ok) {
                throw new Error(response.statusText);
              }
              return response.json();
            })
            .catch(error => {
              Swal.showValidationMessage('Request failed:' + error);
            });
        },
        backdrop: true,
        allowOutsideClick: () => !Swal.isLoading()
      }).then(result => {
        if (result.isConfirmed) {
          Swal.fire({
            title: result.value.login + "'s avatar",
            imageUrl: result.value.avatar_url,
            customClass: {
              confirmButtonText: 'Close me!',
              confirmButton: 'btn btn-primary'
            }
          });
        }
      });
    };
  }

  // Alert With Functional Confirm Button
  if (confirmText) {
    confirmText.onclick = function () {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
      });
    };
  }

  // Alert With Functional Confirm Cancel Button
  if (confirmColor) {
    confirmColor.onclick = function () {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(function (result) {
        if (result.value) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Cancelled',
            text: 'Your imaginary file is safe :)',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
      });
    };
  }
})();
