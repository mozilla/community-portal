jQuery(function() {
  function getFilter(option) {
    const filter = option.dataset.filter;
    return filter;
  }

  const $filters = jQuery(".events__filter__option");
  $filters.each((i, filter) => {
    jQuery(filter).on("change", function(e) {
      const value = encodeURI(e.target.value);
      const filterTitle = getFilter(e.target);
      const url = new URL(location.href);
      const params = new URLSearchParams(url.search.slice(1));
      if (params.has(filterTitle.toLowerCase())) {
        url.searchParams.set(filterTitle.toLowerCase(), value);
        window.location.href = url;
      } else {
        url.searchParams.set(filterTitle.toLowerCase(), value);
        window.location.href = url;
      }
    });
  });
});
