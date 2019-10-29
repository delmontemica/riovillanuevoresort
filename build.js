const fs = require('fs');
const minify = require('babel-minify');
const babel = require('babel-core');
const uglifycss = require('uglifycss');

const files = require('./files');

const options = {
  presets: [
    [
      'env',
      {
        targets: {
          browsers: ['defaults']
        }
      }
    ]
  ]
};

if (!fs.existsSync('build')) {
  fs.mkdirSync('build');
}

if (!fs.existsSync('dist')) {
  fs.mkdirSync('dist');
}

(async function() {
  for (var css of files.css) {
    console.log('Processing: ' + css.output);
    replaceContent(
      css.output,
      uglifycss.processFiles(css.files, {
        expandVars: true,
        uglyComments: true
      })
    );
  }

  for (var js of files.js) {
    if (js.output) {
      var output = [];
      for (var file of js.files) {
        console.log('Processing: ' + file);
        if (typeof js.transform !== undefined && js.transform === false) {
          var code = fs.readFileSync(file, 'utf8');
        } else {
          var { code } = await transform(file, options);
        }
        output.push(code);
      }
      replaceContent(js.output, output.join('\n'));
    } else {
      for (var file of js.files) {
        console.log('Processing: ' + file);
        if (typeof js.transform !== undefined && js.transform === false) {
          var code = fs.readFileSync(file, 'utf8');
        } else {
          var { code } = await transform(file, options);
        }
        replaceContent(file.replace('js/', js.outputDir), code);
      }
    }
  }
})();

function replaceContent(file, text) {
  return new Promise((resolve, reject) => {
    fs.writeFile(file, text, function(err) {
      if (err) reject(err);
      resolve();
    });
  });
}

function transform(file, options) {
  return new Promise((resolve, reject) => {
    babel.transformFile(file, options, function(err, result) {
      if (err) reject(err);
      resolve(result);
    });
  });
}
