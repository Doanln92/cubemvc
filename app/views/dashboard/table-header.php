                    @$req = request();
                    @foreach($name_list as $name => $text)
                    <th>
                        @if(in_array($name,$sort_list))
                            <?php
                                $sort_icon = '';
                                $args = array(
                                    'sortby' => $name
                                );
                                if($req->get('sortby')==$name){
                                    $od = $req->get('orderby');
                                    if($od && strtoupper($od)!='ASC'){
                                        $args['orderby'] = 'ASC';
                                        $sort_icon = ' <i class="fa fa-sort-desc" aria-hidden="true"></i>';
                                    }
                                    else{
                                        $args['orderby'] = 'DESC';
                                        $sort_icon = ' <i class="fa fa-sort-asc" aria-hidden="true"></i>';
                                    }
                                }
                            ?>
                            <a href="{{reqUrl(get_current_url(true),$args)}}">{{$text.$sort_icon}}</a>
                        @else
                            {{$text}}
                        @endif
                    </th>
                    @endforeach