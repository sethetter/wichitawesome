module.exports = {
    version: 'v2.4',
    token: '1450071418617846|xH9wnEYA25GVQYGBfgHGYJfWGaA',
    pageToVenue: function(venueId) {
        return $.getJSON(
            'https://graph.facebook.com/'+this.version+'/'+venueId,
            {fields: 'name,location,phone,about,website', access_token: this.token}
        ).done(function(venue){ 
            if(venue.hasOwnProperty('website')) {
                venue.website = venue.website.replace('www.', '');
            }
        });
    },
    pageToEvent: function(eventId) {
        return $.getJSON(
            'https://graph.facebook.com/'+this.version+'/'+eventId,
            {access_token: this.token}
        );
    }
};