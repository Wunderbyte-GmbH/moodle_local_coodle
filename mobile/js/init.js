// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

async syncData2 = () => {
    console.log('test');
    const currentSiteId = CoreSites.getCurrentSiteId();
    console.warn('my sync');
            // Using syncOnlyOnWifi false to force manual sync.
            try {
                await CoreSettingsHelper.synchronizeSite(false, currentSiteId);
            } catch (error) {
                CoreDomUtils.showErrorModalDefault(error, 'core.settings.sitesyncfailed', true);
            }
            this.refreshContent();
}
