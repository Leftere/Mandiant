const src = './app'
const dest = './build'

module.exports = {
  app: src,
  build: dest,
  browserSync: {
    ui: false,
    server: false,
    open: false,
    notify: false,
    port: 3000
  },
  favicon: {
    src: `${src}/favicon*.*`,
    dest: dest
  },
  fonts: {
    src: `${src}/fonts/**/*`,
    dest: `${dest}/fonts`
  },
  images: {
    src: `${src}/images/**`,
    dest: `${dest}/images`
  },
  scripts: {
    all: `${src}/scripts/**/*.js`,
    modules: `${src}/scripts/modules`,
    src: `${src}/scripts/app.js`,
    dest: `${dest}/scripts`,
    libsSrc: `${src}/scripts/libs/**/*.js`,
    libsDest: `${dest}/scripts/libs/`
  },
  styles: {
    src: [
      `${src}/styles/**/*.{sass,scss}`,
      `!${src}/styles/variables.scss`
    ],
    dest: `${dest}/styles`
  },
  twig: {
    data: `${src}/json`,
    dest: dest,
    global: `${src}/json/global.json`,
    pattern: `${src}/*.twig`,
    source: src,
    src: `${src}/*.twig`,
    namespaces: {
      includes: `${src}/includes`
    },
    watchSrc: [
      `${src}/**/*.twig`,
      `${src}/json/*.json`
    ]
  },
  variables: {
    src: `${src}/styles/variables.scss`,
    dest: `${src}/json`
  }
}
