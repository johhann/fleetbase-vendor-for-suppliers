import Controller from "@ember/controller"
import { tracked } from "@glimmer/tracking"
import { action } from "@ember/object"
import { inject as service } from "@ember/service"
import { debounce } from "@ember/runloop"

export default class VendorsController extends Controller {
  @service router
  @service notifications
  @service modalsManager

  @tracked vendors = []
  @tracked isLoading = false
  @tracked search = ""
  @tracked status = ""

  queryParams = ["page", "limit", "search", "status"]
  @tracked page = 1
  @tracked limit = 10

  @action
  searchVendors(query) {
    this.search = query
    debounce(this, this.refreshModel, 300)
  }

  @action
  filterByStatus(status) {
    this.status = status
    this.page = 1
    this.refreshModel()
  }

  @action
  createVendor() {
    this.router.transitionTo("vendors.new")
  }

  @action
  viewVendor(vendor) {
    this.router.transitionTo("vendors.view", vendor.id)
  }

  @action
  editVendor(vendor) {
    this.router.transitionTo("vendors.edit", vendor.id)
  }

  @action
  deleteVendor(vendor) {
    this.modalsManager.confirm({
      title: "Delete Vendor",
      body: `Are you sure you want to delete ${vendor.name}? This action cannot be undone.`,
      acceptButtonText: "Delete",
      acceptButtonScheme: "danger",
      confirm: async () => {
        try {
          await vendor.destroyRecord()
          this.notifications.success(`${vendor.name} has been deleted successfully.`)
          this.refreshModel()
        } catch (error) {
          this.notifications.serverError(error)
        }
      },
    })
  }

  @action
  downloadQrCode(vendor) {
    if (vendor.qrCodeUrl) {
      const link = document.createElement("a")
      link.href = vendor.qrCodeUrl
      link.download = `${vendor.name}-qr-code.png`
      link.click()
    }
  }

  refreshModel() {
    this.router.refresh()
  }
}
