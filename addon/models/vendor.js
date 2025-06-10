import Model, { attr } from "@ember-data/model"

export default class VendorModel extends Model {
  @attr("string") name
  @attr("string") email
  @attr("string") phone
  @attr("string") address
  @attr("string") status
  @attr("string") qrCodeUrl
  @attr("string") qrCodeData
  @attr("string") scanUrl
  @attr("date") createdAt
  @attr("date") updatedAt

  get isActive() {
    return this.status === "active"
  }

  get statusBadgeClass() {
    return this.isActive ? "badge-success" : "badge-warning"
  }

  get displayStatus() {
    return this.status?.charAt(0).toUpperCase() + this.status?.slice(1)
  }
}
