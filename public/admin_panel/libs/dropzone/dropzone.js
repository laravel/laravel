import Dropzone from 'dropzone/dist/dropzone';

Dropzone.autoDiscover = false;

// File upload progress animation
Dropzone.prototype.uploadFiles = function (files) {
  const minSteps = 6;
  const maxSteps = 60;
  const timeBetweenSteps = 100;
  const bytesPerStep = 100000;
  const isUploadSuccess = true;

  const self = this;

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

    for (let step = 0; step < totalSteps; step++) {
      const duration = timeBetweenSteps * (step + 1);

      setTimeout(
        (function (file, totalSteps, step) {
          return function () {
            file.upload = {
              progress: (100 * (step + 1)) / totalSteps,
              total: file.size,
              bytesSent: ((step + 1) * file.size) / totalSteps
            };

            self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
            if (file.upload.progress === 100) {
              if (isUploadSuccess) {
                file.status = Dropzone.SUCCESS;
                self.emit('success', file, 'success', null);
              } else {
                file.status = Dropzone.ERROR;
                self.emit('error', file, 'Some upload error', null);
              }

              self.emit('complete', file);
              self.processQueue();
            }
          };
        })(file, totalSteps, step),
        duration
      );
    }
  }
};

export { Dropzone };
