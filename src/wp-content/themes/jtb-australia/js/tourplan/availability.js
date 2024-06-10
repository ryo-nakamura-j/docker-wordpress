var TourplanAvailability = function(initialiser) {
	$.extend(this, initialiser);
	this.pricePerSCU = this.TotalPrice / this.Scu;
}