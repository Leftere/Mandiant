// This particular flavor also includes a tiny Promise shim for cross-browser compatibility

const FontFaceObserver = require('fontfaceobserver/fontfaceobserver.js')
const html = document.documentElement

// Replace these with your project web fonts
const thin = new FontFaceObserver('Barlow', {
  'font-weight': 100
})
const light = new FontFaceObserver('Barlow', {
  'font-weight': 300
})
const normal = new FontFaceObserver('Barlow',{
  'font-weight': 400
})
const medium = new FontFaceObserver('Barlow',{
  'font-weight': 500
})
const semibold = new FontFaceObserver('Barlow', {
  'font-weight': 600
})
const bold = new FontFaceObserver('Barlow', {
  'font-weight': 700
})
const extrabold = new FontFaceObserver('Barlow', {
  'font-weight': 700
})
const mono = new FontFaceObserver('PT Mono',{
  'font-weight': 400
})

// Should reference any and all custom Font Families being used in our so we
// don't hide any text during the intial page load.

if (!html.classList.contains('fonts-loaded')) {
  html.classList.add('fonts-loading')

  Promise.all([
    thin.load(),
    light.load(),
    normal.load(),
    medium.load(),
    semibold.load(),
    bold.load(),
    extrabold.load(),
    mono.load()
  ]).then(
    function() {
      html.classList.remove('fonts-loading')
      html.classList.add('fonts-loaded')
      sessionStorage.fontsLoaded = true
    },
    // Timeout fallback if something fails with the promises.
    function() {
      html.classList.remove('fonts-loading')
      html.classList.add('fonts-failed')
    }
  )
}
