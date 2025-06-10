import Route from "@ember/routing/route"
import { inject as service } from "@ember/service"

export default class VendorsNewRoute extends Route {
  @service store

  model() {
    return this.store.createRecord("vendor", {
      status: "active",
    })
  }

  setupController(controller, model) {
    super.setupController(controller, model)
    controller.set("vendor", model)
  }
}
