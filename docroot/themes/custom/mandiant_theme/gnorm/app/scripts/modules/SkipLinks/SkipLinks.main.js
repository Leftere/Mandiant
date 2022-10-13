class SkipLinks {
  constructor(el) {
    this.$el = el
    let links = this.$el.getElementsByTagName('a')

    for (const link of links) {
      let target = document.getElementById(link.hash.substring(1))

      this.skipLink(link, target)
    }

  }
  skipLink(skipper, target) {
    skipper.addEventListener('click', (e) => {
      e.preventDefault()

      if (target) {
        if (target.id === 'maincontent') {
          let mainHeading = target.querySelectorAll('h1, h2, h3, h4, h5, h6')[0]
          target = mainHeading
        }

        if (!(/^(?:a|button|details|input|select|textarea)$/i).test(target.tagName)) {
          target.setAttribute('tabindex', '-1')
        }

        target.focus()
        // target.removeAttribute('tabindex')
      }
    })
  }
}

export default SkipLinks
