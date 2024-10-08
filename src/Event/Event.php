<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Event;

/**
 * Event
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Event
{
    const CONTROLLER_EXECUTE   = 'psx.event_listener.controller_execute';
    const CONTROLLER_PROCESSED = 'psx.event_listener.controller_processed';
    const EXCEPTION_THROWN     = 'psx.event_listener.exception_thrown';
    const REQUEST_INCOMING     = 'psx.event_listener.request_incoming';
    const RESPONSE_SEND        = 'psx.event_listener.response_send';
    const ROUTE_MATCHED        = 'psx.event_listener.route_matched';
}
