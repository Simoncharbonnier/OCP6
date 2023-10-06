<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Monolog\DateTimeImmutable;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\Comment;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // CATEGORIES
        $categories = [];
        $catDatas = [
            [
                'name' => 'Grab',
                'desc' => 'Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab
                    signifie « attraper. » Il existe plusieurs types de grabs selon la position de la saisie et la main
                    choisie pour l\'effectuer, avec des difficultés variables.'
            ],
            [
                'name' => 'Rotation',
                'desc' => 'On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations
                    verticales sont des flips. Le principe est d\'effectuer une rotation horizontale pendant le saut,
                    puis d\'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués.'
            ],
            [
                'name' => 'Flip',
                'desc' => 'Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les
                    back flips, rotations en arrière. Il est possible de faire plusieurs flips à la suite, et d\'ajouter
                    un grab à la rotation.'
            ],
            [
                'name' => 'Slide',
                'desc' => 'Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans
                    l\'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.'
            ],
            [
                'name' => 'One foot',
                'desc' => 'Figures réalisée avec un pied décroché de la fixation, afin de tendre la jambe correspondante
                    pour mettre en évidence le fait que le pied n\'est pas fixé. Ce type de figure est extrêmement dangereuse
                    pour les ligaments du genou en cas de mauvaise réception.'
            ],
            [
                'name' => 'Old school',
                'desc' => 'Le terme old school désigne un style de freestyle caractérisée par en ensemble de figure et
                    une manière de réaliser des figures passée de mode, qui fait penser au freestyle des années 1980 - début 1990.'
            ],
            [
                'name' => 'Autres',
                'desc' => ''
            ]
        ];

        foreach ($catDatas as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setDescription($data['desc']);

            $categories[$data['name']] = $category;
            $manager->persist($category);
        }

        // USERS
        $users = [];
        $usersData = [
            ['username' => 'simon', 'mail' => 'simoncharbonnier03@gmail.com', 'avatar' => '1.jpg'],
            ['username' => 'john', 'mail' => 'johndoe@gmail.com'],
            ['username' => 'shaun', 'mail' => 'shaunwinter@gmail.com', 'avatar' => '3.jpg'],
            ['username' => 'martina', 'mail' => 'martinasnow@gmail.com', 'avatar' => '4.jpg']
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setMail($data['mail']);
            $user->setPassword($this->hasher->hashPassword($user, 'secret'));
            $user->setEnabled(true);
            if (isset($data['avatar'])) {
                $user->setAvatar($data['avatar']);
            }

            $users[] = $user;
            $manager->persist($user);
        }

        // TRICKS
        $tricks = [];
        $tricksData = [
            [
                'name' => 'Ollie',
                'desc' => 'Comme en skateboard, le ollie consiste à faire décoller votre planche du sol, pour effectuer
                    un petit saut.Le but est d\'utiliser le flex de la board pour décoller au dessus du sol. Plus on utilise
                    le flex, plus on décollera du sol. On appelle ça le pop de la board.',
                'category' => $categories['Autres'],
                'images' => [
                    ['name' => 'ollie-0.jpg'],
                    ['name' => 'ollie-1.jpg']
                ],
                'videos' => [
                    ['name' => 'aAefkzI-zS0']
                ]
            ],
            [
                'name' => 'Nollie',
                'desc' => 'Accroupis-toi, déplace ton poids vers l\'avant, puis utilise le nez de ta planche pour sauter.',
                'category' => $categories['Autres'],
                'images' => [
                    ['name' => 'nollie-0.jpg']
                ],
                'videos' => [
                    ['name' => 'aAzP3wNT220']
                ]
            ],
            [
                'name' => 'Tail Press',
                'desc' => 'Le Tail Press est initié en déplaçant ton poids vers l\'arrière de ta planche tout en restant
                    droit et en soulevant le Nose de la neige.',
                'category' => $categories['Autres'],
                'images' => [
                    ['name' => 'tail-press-0.jpg']
                ],
                'videos' => [
                    ['name' => 'Kv0Ah4Xd8d0']
                ]
            ],
            [
                'name' => 'Nose Press',
                'desc' => 'C\'est l\’opposé du Tail Press. Le Nose Press exige que ton poids soit sur l\'avant
                    de la planche, avec l\'arrière décollé de la neige.',
                'category' => $categories['Autres'],
                'images' => [
                    ['name' => 'nose-press-0.jpg']
                ],
                'videos' => [
                    ['name' => 'Px2YuKQVS_g']
                ]
            ],
            [
                'name' => 'Tripod',
                'desc' => 'Va en ligne droite et regarde derrière toi. Fais une grosse pression sur le talon de la planche,
                    puis baisse-toi et touche la neige avec tes mains, en formant un trépied avec le talon de la planche
                    et tes bras. Cette figure demande beaucoup de force au niveau des abdominaux pour tenir en trépied.',
                'category' => $categories['Autres'],
                'images' => [
                    ['name' => 'tripod-0.jpg']
                ],
                'videos' => [
                    ['name' => 'P6crQSwDjJY']
                ]
            ],
            [
                'name' => 'Indy',
                'desc' => 'Attrape le carre des orteils de ta planche, entre les fixations, avec ta main arrière.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'indy-0.jpg'],
                    ['name' => 'indy-1.jpg']
                ],
                'videos' => [
                    ['name' => '6yA3XqjTh_w']
                ]
            ],
            [
                'name' => 'Stalefish',
                'desc' => 'Passe la main derrière ton genou arrière et attrape le carre de ta planche entre les fixations,
                    côté talon, avec ta main arrière.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'stalefish-0.jpg']
                ],
                'videos' => [
                    ['name' => 'f9FjhCt_w2U']
                ]
            ],
            [
                'name' => 'Tail',
                'desc' => 'Attrape le talon de ta planche avec ta main arrière (juste à l\'extrémité, pas sur les côtés).',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'tail-0.jpg']
                ]
            ],
            [
                'name' => 'Weddle',
                'desc' => 'Du nom de Chris Weddle, l\'inventeur, attrape le carre des orteils entre les fixations avec ta main avant.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'weddle-0.jpg']
                ],
                'videos' => [
                    ['name' => 'c1vfTvXjVxY']
                ]
            ],
            [
                'name' => 'Melon',
                'desc' => 'Passe la main avant derrière ton genou et attrape le bord des talons entre les fixations.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'melon-0.jpg']
                ],
                'videos' => [
                    ['name' => 'OMxJRz06Ujc']
                ]
            ],
            [
                'name' => 'Method',
                'desc' => 'À partir de la prise du Melon, étends tes jambes de façon à ce que ton corps ait presque la
                    forme de la queue d\'un scorpion, puis cherche à atteindre le ciel avec ta main arrière.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'method-0.jpg']
                ],
                'videos' => [
                    ['name' => 'lunYxCQrs1E']
                ]
            ],
            [
                'name' => 'Nose',
                'desc' => 'Attrape l\'extrémité avant de ta planche avec ta main avant.',
                'category' => $categories['Grab'],
                'images' => [
                    ['name' => 'nose-0.jpg']
                ],
                'videos' => [
                    ['name' => 'gZFWW4Vus-Q']
                ]
            ],
            [
                'name' => 'Backflip',
                'desc' => 'Un Backflip fait tourner la planche perpendiculairement à la neige, tu fais donc un Flip
                    directement en arrière, en stabilisant la planche lors de l\'atterrissage.',
                'category' => $categories['Flip'],
                'images' => [
                    ['name' => 'backflip-0.jpg']
                ],
                'videos' => [
                    ['name' => 'arzLq-47QFA'],
                    ['name' => '_8TBfD5VPnM'],
                ]
            ],
            [
                'name' => 'Frontflip',
                'desc' => 'Tout comme le Tamedog, le Frontflip te demande de faire un Nose-Press et un Nollie sur un bord.
                    Tu tends ensuite les deux mains vers l\'avant pour amorcer le saut périlleux et remettre la planche
                    en place pour l\'atterrissage.',
                'category' => $categories['Flip'],
                'images' => [
                    ['name' => 'frontflip-0.jpg']
                ],
                'videos' => [
                    ['name' => 'BVeAbNIHktE']
                ]
            ],
            [
                'name' => 'Rodéo',
                'desc' => 'Un Rodéo est un Frontflip avec un twist. Littéralement. Lorsque tu arrives sur le rebord
                    du saut, déclenche un virage Frontside. Puis, décolle la carre des orteils de ta planche,
                    en continuant la rotation, de façon à effectuer un Frontflip avec un Frontside 180, puis atterris en Switch.',
                'category' => $categories['Flip'],
                'videos' => [
                    ['name' => 'vf9Z05XY79A']
                ]
            ],
            [
                'name' => 'Backside Rodéo',
                'desc' => 'L\'inverse du Rodéo, le Backside Rodéo consiste à amorcer un virage Backside à partir du saut,
                    à décoller la carre du talon, puis à effectuer un Backflip avec un Switch 180 à l\'atterrissage.',
                'category' => $categories['Flip'],
                'images' => [
                    ['name' => 'backside-rodeo-0.jpg']
                ],
                'videos' => [
                    ['name' => 'QX6yvs6uTVg']
                ]
            ],
            [
                'name' => 'Corked Spin',
                'desc' => 'Un Corked Spin ajoute simplement un front ou un Backflip dans un flat spin. Tu l\'entendras
                    généralement en compétition lorsque les pros lancent des Back Double Corked 10s ou des Cabs Triple
                    Cork 14s. Mais n\'importe quel spin peut être "corké", comme les Rodéos ci-dessus.',
                'category' => $categories['Flip'],
                'images' => [
                    ['name' => 'corked-spin-0.jpg']
                ],
                'videos' => [
                    ['name' => 'qqNV0tI3Z4k']
                ]
            ],
            [
                'name' => '50/50',
                'desc' => 'Il s\'agit de chevaucher un rail ou un box avec ta planche en ligne droite sur la structure.',
                'category' => $categories['Slide'],
                'images' => [
                    ['name' => '50-50-0.jpg']
                ],
                'videos' => [
                    ['name' => 'e-7NgSu9SXg']
                ]
            ],
            [
                'name' => 'Frontside Boardslide',
                'desc' => 'Il s\'agit de glisser jusqu\'au rail sur ton côté arrière, puis de sauter dessus avec le
                    nez de la planche au-dessus du rail. Tu atterris avec le rail entre tes fixations, ta planche
                    perpendiculaire à la structure.',
                'category' => $categories['Slide'],
                'videos' => [
                    ['name' => 'WRjNFodnOHk']
                ]
            ],
            [
                'name' => 'Frontside Lipslide',
                'desc' => 'Il s\'agit de glisser jusqu\'au rail sur ton côté avant, puis de sauter dessus avec le
                    talon de la planche au-dessus du rail. Tu atterris avec le rail entre tes fixations.',
                'category' => $categories['Slide'],
                'videos' => [
                    ['name' => 'b40a9fCYJ_8']
                ]
            ],
            [
                'name' => 'Backslide Boardslide',
                'desc' => 'Approche le rail sur ton côté avant et saute avec la spatule avant au-dessus du rail.
                    Le rail se place entre tes fixations, mais avec ton talon en tête,
                    c\'est-à-dire en glissant sur la structure vers l\'arrière.',
                'category' => $categories['Slide'],
                'videos' => [
                    ['name' => 'R3OG9rNDIcs']
                ]
            ],
            [
                'name' => 'Backslide Lipslide',
                'desc' => 'Approche le rail sur ton côté arrière et saute avec la talon de la planche au-dessus du rail.
                    Le rail se place entre tes fixations, tu fais cela avec tes talons vers l\'avant.',
                'category' => $categories['Slide'],
                'videos' => [
                    ['name' => 'pfkiK_RBsNc']
                ]
            ],
            [
                'name' => 'Bluntslide',
                'desc' => 'Un Bluntslide signifie que le rail se trouve sous l\'une de tes fixations au lieu d\'être au
                    milieu de la planche. Tu verras souvent cela comme un Boardslide Frontside Blunt, ou un Lipslide
                    Backside Blunt, selon la façon dont tu es arrivé sur le rail et la direction dans laquelle tu vas.',
                'category' => $categories['Slide'],
                'images' => [
                    ['name' => 'bluntslide-0.jpg'],
                    ['name' => 'bluntslide-1.jpg']
                ],
                'videos' => [
                    ['name' => 'Nkotow1RyaQ']
                ]
            ],
        ];

        foreach ($tricksData as $data) {
            $user = $users[array_rand($users)];

            $trick = new Trick();
            $trick->setName($data['name']);
            $trick->setDescription($data['desc']);
            $trick->setUser($user);
            $trick->setCategory($data['category']);
            $trick->setCreatedAt(new DateTimeImmutable('now'));
            $trick->setUpdatedAt(new DateTimeImmutable('now'));

            $tricks[] = $trick;
            $manager->persist($trick);

            if (isset($data['images'])) {
                foreach ($data['images'] as $data) {
                    $image = new Image();
                    $image->setName($data['name']);
                    $image->setTrick($trick);

                    $manager->persist($image);
                }
            }

            if (isset($data['videos'])) {
                foreach ($data['videos'] as $data) {
                    $video = new Video();
                    $video->setName($data['name']);
                    $video->setTrick($trick);

                    $manager->persist($video);
                }
            }
        }

        // COMMENTS
        $commentsData = [
            [
                'message' => 'J\'adore cette figure !'
            ],
            [
                'message' => 'Impossible ! C\'est si difficile à réaliser..'
            ],
            [
                'message' => 'C\'est clairement la plus stylée de toutes !'
            ],
            [
                'message' => 'Shaun a réussi à l\'entraînement !!!'
            ],
            [
                'message' => 'Tu es certain que la figure se fait de cette manière ?'
            ],
            [
                'message' => 'C\'est un classique !'
            ],
            [
                'message' => 'Et surtout bien penser à l\'atterrissage.. Sinon c\'est la blessure assurée'
            ],
            [
                'message' => 'J\'ai réussi à le faire grâce à la vidéo, très intéressant'
            ]
        ];

        foreach ($commentsData as $data) {
            $user = $users[array_rand($users)];
            $trick = $tricks[array_rand($tricks)];

            $comment = new Comment();
            $comment->setUser($user);
            $comment->setTrick($trick);
            $comment->setMessage($data['message']);
            $comment->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($comment);
        }
        $manager->flush();
    }
}
