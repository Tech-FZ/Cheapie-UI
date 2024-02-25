function search(address) {
    var citycb = document.getElementById('city');
    var search = document.getElementById('searchbar');
    var searchfilter = search.value.toLowerCase();
    var cityScrape = citycb.value;
    window.location.href = "searchRes.php?page=search&searchQuery=" + searchfilter + "&city=" + cityScrape;
}