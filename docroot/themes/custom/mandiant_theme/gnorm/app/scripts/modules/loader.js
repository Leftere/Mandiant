import modules from './index'

const init = () => {
  const dataModules = document.querySelectorAll('[data-module]')
  for (const dataModule of dataModules) {
    const name = dataModule.dataset.module
    if (modules.hasOwnProperty(name)) {
      modules[name](dataModule)
    }
  }
}

export default init
