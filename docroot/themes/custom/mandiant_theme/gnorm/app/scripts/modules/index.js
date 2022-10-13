const modules = {
  CallNow: ($el) => {
    import('./CallNow/CallNow.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  EmailNow: ($el) => {
    import('./EmailNow/EmailNow.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  Header: ($el) => {
    import('./Header/Header.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  HeroContinue: ($el) => {
    import('./HeroContinue/HeroContinue.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  MainNav: ($el) => {
    import('./MainNav/MainNav.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  ResourceFilter: ($el) => {
    import('./ResourceFilter/ResourceFilter.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  },
  SkipLinks: ($el) => {
    import('./SkipLinks/SkipLinks.main').then((Module) => {
      new Module.default($el)
    }).catch((e) => console.error(e))
  }
}

export default modules
