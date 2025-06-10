import Component from "@glimmer/component"
import { tracked } from "@glimmer/tracking"
import { action } from "@ember/object"
import { inject as service } from "@ember/service"

export default class QrScannerComponent extends Component {
  @service fetch
  @service notifications

  @tracked isScanning = false
  @tracked scannedData = null
  @tracked vendor = null
  @tracked error = null

  @action
  async startScanning() {
    try {
      this.isScanning = true
      this.error = null

      // Check if browser supports camera
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        throw new Error("Camera not supported in this browser")
      }

      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: "environment" },
      })

      const video = this.element.querySelector("#scanner-video")
      video.srcObject = stream
      video.play()

      // Initialize QR code scanner (you would use a library like jsQR here)
      this.initializeScanner(video)
    } catch (error) {
      this.error = error.message
      this.isScanning = false
      this.notifications.error("Failed to start camera: " + error.message)
    }
  }

  @action
  stopScanning() {
    const video = this.element.querySelector("#scanner-video")
    if (video && video.srcObject) {
      const tracks = video.srcObject.getTracks()
      tracks.forEach((track) => track.stop())
      video.srcObject = null
    }
    this.isScanning = false
  }

  @action
  async processScannedData(qrData) {
    try {
      const response = await this.fetch.request("/api/vendors/scan", {
        method: "POST",
        body: JSON.stringify({ qr_data: qrData }),
        headers: {
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        this.vendor = data.vendor
        this.scannedData = qrData
        this.stopScanning()
        this.notifications.success("Vendor found successfully!")
      } else {
        throw new Error("Vendor not found")
      }
    } catch (error) {
      this.notifications.error("Failed to find vendor: " + error.message)
    }
  }

  initializeScanner(video) {
    // This would integrate with a QR scanning library like jsQR
    // For demo purposes, we'll simulate scanning
    setTimeout(() => {
      // Simulate successful scan
      const mockQrData = JSON.stringify({
        vendor_id: "demo-vendor-id",
        name: "Demo Vendor",
        email: "demo@vendor.com",
      })
      this.processScannedData(mockQrData)
    }, 3000)
  }

  willDestroy() {
    super.willDestroy()
    this.stopScanning()
  }
}
