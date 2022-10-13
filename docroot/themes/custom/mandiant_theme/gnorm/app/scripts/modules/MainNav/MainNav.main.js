class MainNav {
  constructor(el) {
    this.$el = el
    this.$navTab = this.$el.querySelectorAll('.dropdown')
    this.$closeBtn = this.$el.querySelector('.js-close-btn')
    this.$dropDown = this.$el.querySelector('.mega-dropdown-menu')

    // this.$closeBtn.addEventListener('click', (e) => {
    //   e.preventDefault(true)
    //   console.log(this.$navTab)
    //   this.$dropDown.setAttribute('aria-expanded', false)
    //   Object.keys(this.$navTab).forEach((key) => {
    //     console.log(key, this.$navTab[key].classList)
    //     this.$navTab[key].classList.remove('open')
    //   })
    // }) 

    
  }
   
}
  
export default MainNav
