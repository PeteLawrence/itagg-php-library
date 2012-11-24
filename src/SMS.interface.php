<?php

interface SMS {
        function addRecipient($recipient);
        function removeRecipient($recipient);
        function getRecipients();
        function getSender();
        function setSender($sender);
        function setMessage($message);
        function send();
}