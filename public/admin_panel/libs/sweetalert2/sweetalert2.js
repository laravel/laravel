import * as SwalPlugin from 'sweetalert2/dist/sweetalert2';

const Swal = SwalPlugin.mixin({
  buttonsStyling: false,
  customClass: {
    confirmButton: 'btn btn-primary',
    cancelButton: 'btn btn-label-danger',
    denyButton: 'btn btn-label-secondary'
  }
});

export { Swal };
