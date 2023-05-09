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

/**
 * This file is part of the Moodle apps support for the choicegroup plugin.
 * Defines some "providers" in the app init process so they can be used by all group choices.
 *
 * @copyright   2023 Thomas Winkler
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


import { Component, Input, Output, EventEmitter, OnInit, ViewChild, ElementRef } from '@angular/core';
import { CoreConfig } from '@services/config';
import { CoreEvents } from '@singletons/events';
import { CoreSites } from '@services/sites';
import { CoreUtils } from '@services/utils/utils';
import { CoreTextUtils } from '@services/utils/text';
import { CoreConstants } from '@/core/constants';
import { CoreForms } from '@singletons/form';
import { CorePlatform } from '@services/platform';
import { AddonPrivateFilesHelper } from '@addons/privatefiles/services/privatefiles-helper';
import { CoreNetwork } from '@services/network';
import { CoreDomUtils } from '@services/utils/dom';
import { AddonPrivateFilesGetUserInfoWSResult } from '@addons/privatefiles/services/privatefiles';


export class ClassName implements OnInit {


    @ViewChild('messageForm') formElement!: ElementRef;

    filesInfo?: AddonPrivateFilesGetUserInfoWSResult;
    filesLoaded = false; // Whether the files are loaded.

    constructor() {

    }

    ngOnInit(): void {
    }


    async uploadFile(): Promise<void>{
        console.log('upload');

        try {
            await AddonPrivateFilesHelper.uploadPrivateFile(this.filesInfo);

            // File uploaded, refresh the list.
            this.filesLoaded = false;

            // await CoreUtils.ignoreErrors(this.refreshFiles());

            this.filesLoaded = true;
        } catch (error) {
            // CoreDomUtils.showErrorModalDefault(error, 'core.fileuploader.errorwhileuploading', true);
        }
    }


}
