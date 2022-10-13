import customSelect from 'custom-select'

class EmailNow {
  constructor(el) {
    this.$el = el
    this.$select = this.$el.querySelector('.email-now__select')
    this.$link = this.$el.querySelector('.email-now__link')

    customSelect(this.$select)

    this.$select.addEventListener('change', () => {
      this.updateLink(this.$select.value)
    })
  }

  updateLink(value) {
    if (value.charAt(0) === '/') {
      value = value.substring(1)
    }
    this.$link.setAttribute('href', `mailto:${value}`)
    this.$link.innerHTML = `${value}`
  }
}

export default EmailNow
