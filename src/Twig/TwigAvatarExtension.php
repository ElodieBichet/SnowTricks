<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\Length;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigAvatarExtension extends AbstractExtension
{

    protected $bgAllowedColors;

    public function __construct(array $bgAllowedColors)
    {
        $this->bgAllowedColors = $bgAllowedColors;
    }

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
        $bgAllowedColors = $this->bgAllowedColors;
        // Use user id to define a unic color by user
        $bgAvatar = $bgAllowedColors[$user->getId() % count($bgAllowedColors)];
        // Delete dots and replace spaces with plus signs in user's fullname
        $username = str_replace('.', '', str_replace(' ', '%2b', trim($user->getFullname())));

        // Set the src of the image
        $avatarUrl =
            'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->getEmail())))
            . '?d=https%3A%2F%2Fui-avatars.com%2Fapi%2F'
            . $username . '%2F' . $size . '%2F' . $bgAvatar
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
