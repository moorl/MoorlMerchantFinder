import { searchRankingPoint } from 'src/app/service/search-ranking.service';

const defaultSearchConfiguration = {
    _searchable: true,
    name: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
    company: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
    city: {
        _searchable: true,
        _score: searchRankingPoint.HIGH_SEARCH_RANKING,
    },
};

export default defaultSearchConfiguration;
