<?php
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
 * Local cohorts module capability definition
 *
 * @package         local_coodle
 * @author          Christian Badusch
 * @copyright       2022 Wunderbyte GmbH
 * @license         http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace local_coodle\output;

class mobile {

    public static function view_hello() {
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<ion-content>
                    <ion-grid>
                      <ion-row>
                        <ion-col class="landingcol col1">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 1</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="archive-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                        <ion-col class="landingcol col2">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 2</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="chatbox-ellipses-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                      </ion-row>
                      <ion-row>
                        <ion-col class="landingcol col3">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 3</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="file-tray-full-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                        <ion-col class="landingcol col4">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 4</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="person-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                      </ion-row>
                      <ion-row>
                        <ion-col class="landingcol col5">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 5</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="person-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                        <ion-col class="landingcol col6">
                          <ion-button class="gridbutton">
                            <ion-grid>
                              <ion-row>
                                <ion-label>Button 6</ion-label>
                              </ion-row>
                              <ion-row>
                                <ion-icon name="reorder-four-outline"></ion-icon>
                              </ion-row>
                            </ion-grid>
                          </ion-button>
                        </ion-col>
                      </ion-row>
                    </ion-grid>
                  </ion-content>
                  ',
                ],
            ],
        ];
    }

}