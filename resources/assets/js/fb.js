var $ = require('jquery');
module.exports = {
    version: 'v2.4',
    token: 'CAAUm1QZCOPZCYBAEDiWXtB3jwyZAFfCKgNzTN4c8U9qA5ZBBQgnwVzUJye6P64Frbn04aRZAFkFZB4a8tR86gNoPCukFCBq8MmbMD7PzSE2WpiSjZBI6lnsHqCAvkW0uj4BoIt2H0QOqhIERyZBtvZCZCyafddl9Q4BUGZAFrNztiN9uHOthZCLBoWIa',
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