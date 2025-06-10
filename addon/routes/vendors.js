import Route from "@ember/routing/route"
import { inject as service } from "@ember/service"

export default class VendorsRoute extends Route {
  @service store
  @service notifications

  queryParams = {
    page: { refreshModel: true },
    limit: { refreshModel: true },
    search: { refreshModel: true },
    status: { refreshModel: true },
  }

  model(params) {
    return this.store.query("vendor", {
      page: params.page || 1,
      limit: params.limit || 10,
      search: params.search,
      status: params.status,
    })
  }

  setupController(controller, model) {
    super.setupController(controller, model)
    controller.set("vendors", model)
  }
}
