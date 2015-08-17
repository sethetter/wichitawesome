module.exports = {
    items: [],
    get: function(key) {
        if (key in this.items) {
            return this.items[key];
        }
        return null;
    },
    set: function(key, item) {
        this.items[key] = item;
    }
};