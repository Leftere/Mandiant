class ResourceFilter {
  constructor(el) {
    this.$el = el
    this.$allButtons = document.querySelectorAll('.resource-filter__button')
    this.$allPanels = document.querySelectorAll('.resource-filter__panel')
    this.$button = this.$el.querySelector('.resource-filter__button')
    this.$panel = this.$el.querySelector('.resource-filter__panel')
    this.$container = this.$el.closest('.search--filter')

    this.$button.addEventListener('click', this.handleButtonClick)
  }

  handleButtonClick = () => {
    const isExpanded = JSON.parse(this.$button.getAttribute('aria-expanded'))

    if (isExpanded) {
      this.closeAllPanels()
    } else {
      this.openPanel()
    }
  }

  openPanel() {
    this.closeAllPanels()
    this.$button.setAttribute('aria-expanded', true)
    this.$panel.setAttribute('aria-hidden', false)
    this.$container.classList.add('panel-is-open')
  }

  closeAllPanels() {
    this.$allButtons.forEach((button) => {
      button.setAttribute('aria-expanded', false)
    })

    this.$allPanels.forEach((panel) => {
      panel.setAttribute('aria-hidden', true)
    })

    this.$container.classList.remove('panel-is-open')
  }
}

export default ResourceFilter
