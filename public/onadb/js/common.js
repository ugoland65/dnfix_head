const commonJs = (function() {

    function search() {

        const headerSearchText = document.getElementById('header_search_text');
        var params = [];

        if (headerSearchText.value) {
            params.push('search_value=' + encodeURIComponent(headerSearchText.value));
        }

        // 항상 최상위 루트에서 검색
        if (params.length > 0) {
            window.location.href = window.location.origin + '/?' + params.join('&');
        } else {
            window.location.href = window.location.origin + '/';
        }

    }

    return {
        search,
    }

})();

document.getElementById('header_search_btn').addEventListener('click', commonJs.search);