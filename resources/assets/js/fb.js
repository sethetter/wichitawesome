module.exports = {
    version: 'v2.4',
    token: '1450071418617846|xH9wnEYA25GVQYGBfgHGYJfWGaA',
    pageToVenue: function(venueId) {
        return $.getJSON(
            'https://graph.facebook.com/'+this.version+'/'+venueId,
            {fields: 'name,location,phone,about', access_token: this.token}
        );
    },
    pageToEvent: function(eventId) {
        return $.getJSON(
            'https://graph.facebook.com/'+this.version+'/'+eventId,
            {access_token: this.token}
        );
    }
};