<?php

namespace App\Models;

class Parser
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        $this->normalizeData();
        return [
            "email" => $this->findEmail(),
            "website" => $this->findWebsite(),
            "phone" => $this->findPhoneOrFax(),
            "fax" => $this->findPhoneOrFax(),
            "name" => $this->findName(),
            "company" => $this->findCompany(),
            "address" => $this->findAddress(),
        ];
    }

    private function normalizeData(): void
    {
        foreach ($this->data as $key => $line) {
            $line = trim(preg_replace('/\s\s+/', ' ', $line));
            $this->data[$key] = $line;
            if ($this->removeMessageRegards($line)) {
                unset($this->data[$key]);
            }
        }
    }

    private function removeMessageRegards(string $element): bool {
        $messageRegards = ["yours sincerely", "best regards", "sincerely"];
        foreach ($messageRegards as $messageRegard) {
            if (str_contains(strtolower($element), $messageRegard)) {
                return true;
            }
        }
        return false;
    }

    private function findEmail(): ?string
    {
        $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
        foreach ($this->data as $key => $line) {
            preg_match($pattern, $line, $matches);
            if(!empty($matches)) {
                unset($this->data[$key]);
                return $matches[0];
            }
        }
        return null;
    }

    private function findWebsite(): ?string
    {
        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        foreach ($this->data as $key => $line) {
            preg_match($pattern, $line, $matches);
            if(!empty($matches)) {
                unset($this->data[$key]);
                return $matches[0];
            }
        }
        return null;
    }

    private function findPhoneOrFax(): ?string
    {
        $pattern = '/\+?\d{1,3}(?:[\s\(\-])*(\d{1,4}(?:[\)\-\. ]*\d{1,4})*)\b(?! *\d{5}\b)/';
        foreach ($this->data as $key => $line) {
            $element = str_replace(" ", "", $line);
            preg_match_all($pattern, str_replace(" ", "", $line), $matches, PREG_SET_ORDER);
            if(!empty($matches)) {
                if(mb_strlen($matches[0][0]) <= 5) continue;
                if (count($matches) > 1) {
                    $this->data[] = str_replace(trim($matches[0][0]), "", $element);
                    unset($this->data[$key]);
                }
                unset($this->data[$key]);
                return $matches[0][0];
            }
        }
        return null;
    }

    private function findName(): ?string
    {
        foreach ($this->data as $key => $line) {
            $element = trim($line);
            $words = explode(" ", $element);
            if (count($words) >= 2) {
                $potentialName = $words[count($words) - 2];
                $potentialSurname = $words[count($words) - 1];

                $conditions = ctype_upper(substr($potentialName, 0, 1)) && ctype_lower(substr($potentialName, 1))
                    && ctype_upper(substr($potentialSurname, 0, 1)) && ctype_lower(substr($potentialSurname, 1))
                    && !preg_match('~[0-9]+~', $potentialName) && !preg_match('~[0-9]+~', $potentialSurname);

                if ($conditions) {
                    unset($this->data[$key]);
                    return $potentialName . " " . $potentialSurname;
                }
            }
        }
        return null;
    }

    private function findCompany(): ?string
    {
        foreach ($this->data as $key => $line) {
            unset($this->data[$key]);
            if ($line) return $line;
        }
        return null;
    }

    private function findAddress(): ?string
    {
        $address = "";
        foreach ($this->data as $key => $line) {
            if($line) $address .= $line . " ";
        }
        if ($address) return $address;
        return null;
    }
}
