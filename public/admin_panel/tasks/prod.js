const path = require('path');
const { src, dest, series } = require('gulp');
const replace = require('gulp-replace');
const useref = require('gulp-useref');
var uglify = require('gulp-uglify');

module.exports = conf => {
  // Copy templatePath html files and assets to buildPath
  // -------------------------------------------------------------------------------
  const prodCopyTask = function () {
    return src(`${templatePath}/**/*.html`)
      .pipe(dest(buildPath))
      .pipe(src('assets/**/*'))
      .pipe(dest(`${buildPath}/assets/`));
  };

  // Rename assets path
  // -------------------------------------------------------------------------------
  const prodRenameTasks = function () {
    return src(`${buildPath}/*.html`)
      .pipe(replace('../../assets', 'assets'))
      .pipe(dest(buildPath))
      .pipe(src(`${buildPath}/assets/**/*`))
      .pipe(replace('../../assets', 'assets'))
      .pipe(dest(`${buildPath}/assets/`));
  };

  // Combine js vendor assets in single core.js file using UseRef
  // -------------------------------------------------------------------------------
  const prodUseRefTasks = function () {
    return src(`${buildPath}/*.html`).pipe(useref()).pipe(dest(buildPath));
  };

  // Uglify assets/js files
  //--------------------------------------------------------------------------------
  const prodMinifyJSTasks = function () {
    return src(`${buildPath}/assets/js/**/*.js`)
      .pipe(uglify())
      .pipe(dest(`${buildPath}/assets/js/`));
  };

  const prodAllTask = series(prodCopyTask, prodRenameTasks, prodUseRefTasks, prodMinifyJSTasks);

  // Exports
  // ---------------------------------------------------------------------------

  return {
    copy: prodCopyTask,
    rename: prodRenameTasks,
    useref: prodUseRefTasks,
    minifyJS: prodMinifyJSTasks,
    all: prodAllTask
  };
};
