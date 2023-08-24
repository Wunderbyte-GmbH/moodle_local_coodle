console.log('test');
this.openModal = () => {
    this.CONTENT_OTHERDATA.showmodal = !this.CONTENT_OTHERDATA.showmodal;
    this.AlertController.create({header: 'Hallo Welt'}).then(alert => alert.present());
}
