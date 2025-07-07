/**
 *  Pages Authentication
 */

'use strict';
const formAuthentication = document.querySelector('#formAuthentication');

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    // Form validation for Add new record
    if (formAuthentication) {
      const fv = FormValidation.formValidation(formAuthentication, {
        fields: {
          nama: {
            validators: {
              notEmpty: {
                message: 'Please enter your name'
              },
              stringLength: {
                min: 3,
                message: 'Name must be more than 3 characters'
              }
            }
          },
          namaper: {
            validators: {
              notEmpty: {
                message: 'Please enter your company name'
              },
              stringLength: {
                min: 4,
                message: 'Name must be more than 4 characters'
              }
            }
          },
          kodepos: {
            validators: {
              notEmpty: {
                message: 'Please enter your postal code'
              },
              stringLength: {
                min: 3,
                message: 'Postal code must be more than 3 characters'
              }
            }
          },
          noper: {
            validators: {
              notEmpty: {
                message: 'Please enter your phone'
              },
              stringLength: {
                min: 4,
                message: 'Phone must be more than 4 characters'
              }
            }
          },
          alamat: {
            validators: {
              notEmpty: {
                message: 'Please enter your address'
              }
            }
          },
          bidangid: {
            validators: {
              notEmpty: {
                message: 'Please select your business fields'
              }
            }
          },
          negaraid: {
            validators: {
              notEmpty: {
                message: 'Please select your country'
              }
            }
          },
          zonawaktuid: {
            validators: {
              notEmpty: {
                message: 'Please select your time zone'
              }
            }
          },
          username: {
            validators: {
              notEmpty: {
                message: 'Please enter your username'
              },
              stringLength: {
                min: 4,
                message: 'Username must be more than 4 characters'
              }
            }
          },
          email: {
            validators: {
              notEmpty: {
                message: 'Please enter your email'
              },
              emailAddress: {
                message: 'Please enter valid email address'
              }
            }
          },
          'email-username': {
            validators: {
              notEmpty: {
                message: 'Please enter email / username'
              },
              stringLength: {
                min: 4,
                message: 'Username must be more than 4 characters'
              }
            }
          },
          password: {
            validators: {
              notEmpty: {
                message: 'Please enter your password'
              },
              stringLength: {
                min: 4,
                message: 'Password must be more than 4 characters'
              }
            }
          },
          'confirm-password': {
            validators: {
              notEmpty: {
                message: 'Please confirm password'
              },
              identical: {
                compare: function () {
                  return formAuthentication.querySelector('[name="password"]').value;
                },
                message: 'The password and its confirm are not the same'
              },
              stringLength: {
                min: 4,
                message: 'Password must be more than 4 characters'
              }
            }
          },
          terms: {
            validators: {
              notEmpty: {
                message: 'Please agree terms & conditions'
              }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.mb-3'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),

          defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
        init: instance => {
          instance.on('plugins.message.placed', function (e) {
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      });
    }

    //  Two Steps Verification
    const numeralMask = document.querySelectorAll('.numeral-mask');

    // Verification masking
    if (numeralMask.length) {
      numeralMask.forEach(e => {
        new Cleave(e, {
          numeral: true
        });
      });
    }
  })();
});
