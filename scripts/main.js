function search() {
    var citycb = document.getElementById('city');
    var search = document.getElementById('searchbar');
    var searchfilter = search.value.toLowerCase();
    var city = citycb.value;

    window.location.href = "sites/searchRes.php?page=search&searchQuery=\"" + searchfilter + "\"&city=\"" + city + "\"";
}