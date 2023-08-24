this.openModal = () => {
    this.AlertController.create({header: 'Hallo Welt'}).then(alert => alert.present());
}
