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

const that = this;

this.syncData2 = async () => {
    console.log('test');
    const currentSiteId = this.CoreSitesProvider.getCurrentSiteId();
    console.warn('my sync');
    console.warn(currentSiteId);
            // Using syncOnlyOnWifi false to force manual sync.
            try {
                await this.CoreSettingsHelper.synchronizeSite(false, currentSiteId);
            } catch (error) {
                this.CoreDomUtilsProvider.showErrorModalDefault(error, 'core.settings.sitesyncfailed', true);
            }
    this.refreshContent();
}
