<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigAvatarExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('avatar', [$this, 'avatarFilter'], ['is_safe' => ['html']])
        ];
    }

    /**
     * avatarFilter: display a user avatar using gravatar if it exists and ui-avatars.com if not
     */
    public function avatarFilter(User $user, int $size, array $attr = []): string
    {
        // List all allowed background colors for a generated avatar
        $bgColors = ['012A4A/fff', '013A63/fff', '01497C/fff', '2A6F97/fff', '2C7DA0/fff', '468FAF/fff', '61A5C2/fff', '89C2D9', 'A9D6E5'];
        // Use user id to define a unic color by user
        $bgAvatar = $bgColors[$user->getId() % count($bgColors)];

        // Set the src of the image
        $avatarUrl =
            'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->getEmail())))
            . '?d=https%3A%2F%2Fui-avatars.com%2Fapi%2F/'
            . trim($user->getFullname()) . '/' . $size . '/' . $bgAvatar
            . '&s=' . $size;

        // Attributes for <img>
        $imgAttr = '';
        if (count($attr) > 0) {
            foreach ($attr as $name => $value) {
                $imgAttr .= ' ' . $name . '="' . $value . '"';
            }
        }

        $template = '<img src="%s" alt="%s"%s>';

        $avatar = sprintf($template, $avatarUrl, $user->getFullname(), $imgAttr);

        return $avatar;
    }
}
