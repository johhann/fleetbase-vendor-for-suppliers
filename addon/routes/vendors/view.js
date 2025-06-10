import Route from "@ember/routing/route"
import { inject as service } from "@ember/service"

export default class VendorsViewRoute extends Route {
  @service store

  model(params) {
    return this.store.findRecord("vendor", params.vendor_id)
  }

  setupController(controller, model) {
    super.setupController(controller, model)
    controller.set("vendor", model)
  }
}
