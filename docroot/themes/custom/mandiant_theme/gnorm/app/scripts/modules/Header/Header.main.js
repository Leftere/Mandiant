import {
  disableBodyScroll,
  enableBodyScroll,
} from 'body-scroll-lock/lib/bodyScrollLock'

class Header {
  constructor(el) {
    this.$el = el
    this.$body = document.querySelector('#header__top')
    this.$burger = this.$el.querySelector('.hamburger')
    this.$searchButton = this.$el.querySelector('.search')
    this.$utilityNav = document.querySelector('.utility-navigation')
    this.$utilityClose = document.querySelector('.utility-navigation__close')
    this.$searchForm = document.querySelector('.main-nav__search')
    this.$languageButton = this.$el.querySelector('.language')
    this.$languageNav = document.querySelector('.main-nav__language')
    this.$megaNavLink = document.querySelectorAll('.link')
    this.$dropDown = document.querySelectorAll('.dropDown')
    this.$closeBtn = document.querySelectorAll('.close-btn')
    this.$scrolled = false

    // function to close sublinks
    if(this.$closeBtn) {
      this.$closeBtn.forEach((btn) => {
        btn.addEventListener('click', () => {
          btn.parentElement.parentElement.classList.remove('open')
        })
      })
    }
  

    // function to open sublinks
    if (this.$megaNavLink && this.$dropDown) {
      this.$megaNavLink.forEach((link) => {
        link.addEventListener('click', (e) => {
          e.preventDefault()
          let dropDown = link.nextElementSibling
          dropDown.classList.add('open')
        })
      })
    }


    let observer = new IntersectionObserver((entries) => {
      if (entries[0].boundingClientRect.y < 0) {
        this.$el.classList.add('scrolled')
        this.$scrolled = true
      } else {
        this.$el.classList.remove('scrolled')
        this.$scrolled = false
      }
    })

    observer.observe(this.$body)

    if (this.$burger) {
      this.$burger.addEventListener('click', () => {
        if (this.$burger.classList.contains('expanded')) {
          this.closeNav()
        } else {
          this.openNav()
        }
      })
    }

    if (this.$searchButton) {
      this.$searchButton.addEventListener('click', () => {
        if (this.$searchButton.classList.contains('expanded')) {
          this.closeSearch()
        } else {
          this.openSearch()
        }
      })
    }

    // Language button now opens a language switcher page
    // if (this.$languageButton) {
    //   this.$languageButton.addEventListener('click', () => {
    //     if (this.$languageButton.classList.contains('expanded')) {
    //       this.closeLanguage()
    //     } else {
    //       this.openLanguage()
    //     }
    //   })
    // }

    if (this.$utilityClose) {
      this.$utilityClose.addEventListener('click', () => {
        this.closeNav()
      })
    }
  }

  escapeHandler = (e) => {
    if (e.key === 'Escape') {
      this.closeAll()

      document.removeEventListener('keydown', this.escapeHandler)
    }
  }


  openNav() {
    if (this.$burger && this.$utilityNav) {
      this.closeAll()
      document.body.classList.add('utility-nav-is-open')
      this.$burger.classList.add('expanded')
      this.$burger.setAttribute('aria-expanded', true)
      this.$utilityNav.classList.add('show')
      this.$utilityNav.setAttribute('aria-hidden', false)

      if (!this.$scrolled) {
        this.$el.classList.add('scrolled')
      }

      disableBodyScroll(this.$utilityNav)
      document.addEventListener('keydown', this.escapeHandler)
    }
  }

  closeNav() {
    if (this.$burger && this.$utilityNav) {
      document.body.classList.remove('utility-nav-is-open')
      this.$burger.classList.remove('expanded')
      this.$burger.setAttribute('aria-expanded', false)
      this.$utilityNav.classList.remove('show')
      this.$utilityNav.setAttribute('aria-hidden', true)

      if (!this.$scrolled) {
        this.$el.classList.remove('scrolled')
      }

      enableBodyScroll(this.$utilityNav)
    }
  }

  openSearch() {
    if (this.$searchButton && this.$searchForm) {
      this.closeAll()
      document.body.classList.add('search-is-open')
      this.$searchButton.classList.add('expanded')
      this.$searchButton.setAttribute('aria-expanded', true)
      this.$searchForm.classList.add('show')
      this.$searchForm.setAttribute('aria-hidden', false)
      document.addEventListener('keydown', this.escapeHandler)
    }
  }

  closeSearch() {
    if (this.$searchButton && this.$searchForm) {
      document.body.classList.remove('search-is-open')
      this.$searchButton.classList.remove('expanded')
      this.$searchButton.setAttribute('aria-expanded', false)
      this.$searchForm.classList.remove('show')
      this.$searchForm.setAttribute('aria-hidden', true)
    }
  }

  // openLanguage() {
  //   this.closeAll()
  //   document.body.classList.add('language-is-open')
  //   this.$languageButton.classList.add('expanded')
  //   this.$languageButton.setAttribute('aria-expanded', true)
  //   this.$languageNav.classList.add('show')
  //   this.$languageNav.setAttribute('aria-hidden', false)

  //   document.addEventListener('keydown', this.escapeHandler)
  // }

  // closeLanguage() {
  //   document.body.classList.remove('language-is-open')
  //   this.$languageButton.classList.remove('expanded')
  //   this.$languageButton.setAttribute('aria-expanded', false)
  //   this.$languageNav.classList.remove('show')
  //   this.$languageNav.setAttribute('aria-hidden', true)
  // }

  closeAll() {
    this.closeNav()
    this.closeSearch()
    // this.closeLanguage()
  }

}

export default Header
