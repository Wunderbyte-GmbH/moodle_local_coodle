console.log('test');
this.openModal = () => {
    this.CONTENT_OTHERDATA.showmodal = 1;
    this.AlertController.create({header: 'Hallo Welt'}).then(alert => alert.present());
}
