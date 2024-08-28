const state = {
  searchHistory: [],
};

const getters = {
  getSearchHistory: (state) => {
    return state.searchHistory.sort(
      (a, b) => new Date(b.timestamp) - new Date(a.timestamp)
    );
  },
};

const actions = {
  addHistory({ search, column, filter }) {
    let existing = this.searchHistory.find(
      (history) =>
        history.search == search &&
        history.column == column &&
        history.filter == filter
    );

    if (!!existing) {
      existing.timestamp = Date.now();
    } else {
      this.searchHistory.unshift({
        search: search,
        column: column,
        filter: filter,
        timestamp: Date.now(),
      });
    }
    this.searchHistory = this.getSearchHistory;

    if (this.searchHistory.length > 10) {
      this.searchHistory.splice(10, this.searchHistory.length - 10);
    }
  },
  removeHistory(history) {
    let index = this.searchHistory.indexOf(history);
    this.searchHistory.splice(index, 1);
  },
};

export { state, getters, actions };
