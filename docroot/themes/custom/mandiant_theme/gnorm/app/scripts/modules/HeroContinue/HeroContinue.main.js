class HeroContinue {
  constructor(el) {
    this.$el = el
    this.$headerHeight = document.querySelector('.global-header > .header').offsetHeight
    this.$hero = this.$el.closest('.hero')

    this.$el.addEventListener('click', () => {
      const position = this.$hero.offsetHeight - this.$headerHeight

      window.scrollTo({
        top: position,
        behavior: 'smooth'
      })
    })
  }
}

export default HeroContinue
